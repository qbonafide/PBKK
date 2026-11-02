<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KamarModel;
use App\Models\PenghuniModel;
use App\Models\UserModel;

/**
 * CRUD penghuni (khusus admin).
 * Aturan bisnis yang dijaga di sini:
 *  - satu kamar hanya boleh punya satu penghuni aktif,
 *  - menambah/mengaktifkan penghuni otomatis membuat kamar berstatus "terisi",
 *  - menandai penghuni keluar otomatis mengembalikan kamar menjadi "kosong".
 */
class Penghuni extends BaseController
{
    private const FOLDER_KTP = WRITEPATH . 'uploads/ktp/';

    public function index()
    {
        $model   = new PenghuniModel();
        $keyword = (string) ($this->request->getGet('q') ?? '');

        $model->select('penghuni.*, kamar.nomor_kamar, users.username')
            ->join('kamar', 'kamar.id = penghuni.kamar_id', 'left')
            ->join('users', 'users.id = penghuni.user_id', 'left');

        if ($keyword !== '') {
            $model->groupStart()
                ->like('penghuni.nama', $keyword)
                ->orLike('kamar.nomor_kamar', $keyword)
                ->groupEnd();
        }

        service('pager')->only(['q']);

        return view('admin/penghuni/index', [
            'title'   => 'Kelola Penghuni',
            'penghuni' => $model->orderBy('penghuni.status', 'ASC')
                ->orderBy('kamar.nomor_kamar', 'ASC')
                ->paginate(8),
            'pager'   => $model->pager,
            'keyword' => $keyword,
        ]);
    }

    public function create()
    {
        return view('admin/penghuni/form', [
            'title'    => 'Tambah Penghuni',
            'penghuni' => null,
            'akun'     => $this->akunTersedia(),
            'kamar'    => $this->kamarTersedia(),
        ]);
    }

    public function store()
    {
        $aturan = $this->aturanValidasi();
        $file   = $this->request->getFile('foto_ktp');

        if ($file !== null && $file->isValid() && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            $aturan['foto_ktp'] = 'is_image[foto_ktp]|mime_in[foto_ktp,image/jpg,image/jpeg,image/png,image/webp]|max_size[foto_ktp,2048]';
        }

        if (! $this->validate($aturan, $this->pesanValidasi())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model    = new PenghuniModel();
        $kamarId  = (int) $this->request->getPost('kamar_id');
        $userId   = (int) $this->request->getPost('user_id');
        $status   = (string) $this->request->getPost('status');

        if ($model->where('user_id', $userId)->first() !== null) {
            return redirect()->back()->withInput()
                ->with('errors', ['user_id' => 'Akun ini sudah dipakai penghuni lain.']);
        }

        if ($status === 'aktif' && $model->penghuniAktifPerKamar($kamarId) !== null) {
            return redirect()->back()->withInput()
                ->with('errors', ['kamar_id' => 'Kamar ini sudah dihuni. Tandai penghuni lama keluar dulu.']);
        }

        $fotoKtp = simpan_upload($file, self::FOLDER_KTP);

        $db = db_connect();
        $db->transStart();

        $tersimpan = $model->insert([
            'user_id'        => $userId,
            'kamar_id'       => $kamarId,
            'nama'           => (string) $this->request->getPost('nama'),
            'no_hp'          => (string) $this->request->getPost('no_hp'),
            'alamat_asal'    => (string) $this->request->getPost('alamat_asal'),
            'pekerjaan'      => (string) $this->request->getPost('pekerjaan'),
            'foto_ktp'       => $fotoKtp,
            'tanggal_masuk'  => (string) $this->request->getPost('tanggal_masuk'),
            'tanggal_keluar' => $status === 'keluar'
                ? (string) ($this->request->getPost('tanggal_keluar') ?: date('Y-m-d'))
                : null,
            'status'         => $status,
        ]);

        if ($status === 'aktif') {
            $this->ubahStatusKamar($kamarId, 'terisi');
        }

        $db->transComplete();

        if ($tersimpan === false || $db->transStatus() === false) {
            hapus_berkas($fotoKtp === null ? null : self::FOLDER_KTP . $fotoKtp);

            return redirect()->back()->withInput()
                ->with('errors', $model->errors() ?: ['umum' => 'Gagal menyimpan data penghuni.']);
        }

        return redirect()->to(site_url('admin/penghuni'))
            ->with('sukses', 'Penghuni ' . $this->request->getPost('nama') . ' berhasil ditambahkan.');
    }

    public function edit($id = null)
    {
        $penghuni = (new PenghuniModel())->find((int) $id);

        if ($penghuni === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Penghuni tidak ditemukan.');
        }

        return view('admin/penghuni/form', [
            'title'    => 'Edit Penghuni ' . $penghuni['nama'],
            'penghuni' => $penghuni,
            'akun'     => $this->akunTersedia((int) $penghuni['user_id']),
            'kamar'    => $this->kamarTersedia((int) $penghuni['kamar_id']),
        ]);
    }

    public function update($id = null)
    {
        $model    = new PenghuniModel();
        $penghuni = $model->find((int) $id);

        if ($penghuni === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Penghuni tidak ditemukan.');
        }

        $aturan = $this->aturanValidasi();
        $file   = $this->request->getFile('foto_ktp');

        if ($file !== null && $file->isValid() && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            $aturan['foto_ktp'] = 'is_image[foto_ktp]|mime_in[foto_ktp,image/jpg,image/jpeg,image/png,image/webp]|max_size[foto_ktp,2048]';
        }

        if (! $this->validate($aturan, $this->pesanValidasi())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $kamarIdLama = (int) $penghuni['kamar_id'];
        $kamarIdBaru = (int) $this->request->getPost('kamar_id');
        $userId      = (int) $this->request->getPost('user_id');
        $status      = (string) $this->request->getPost('status');

        if ($model->where('user_id', $userId)->where('id !=', (int) $id)->first() !== null) {
            return redirect()->back()->withInput()
                ->with('errors', ['user_id' => 'Akun ini sudah dipakai penghuni lain.']);
        }

        if ($status === 'aktif' && $model->penghuniAktifPerKamar($kamarIdBaru, (int) $id) !== null) {
            return redirect()->back()->withInput()
                ->with('errors', ['kamar_id' => 'Kamar tujuan sudah dihuni penghuni aktif lain.']);
        }

        $fotoBaru  = simpan_upload($file, self::FOLDER_KTP);
        $data      = [
            'user_id'       => $userId,
            'kamar_id'      => $kamarIdBaru,
            'nama'          => (string) $this->request->getPost('nama'),
            'no_hp'         => (string) $this->request->getPost('no_hp'),
            'alamat_asal'   => (string) $this->request->getPost('alamat_asal'),
            'pekerjaan'     => (string) $this->request->getPost('pekerjaan'),
            'tanggal_masuk' => (string) $this->request->getPost('tanggal_masuk'),
            'status'        => $status,
        ];

        if ($status === 'keluar') {
            $data['tanggal_keluar'] = (string) ($this->request->getPost('tanggal_keluar') ?: date('Y-m-d'));
        } else {
            $data['tanggal_keluar'] = null;
        }

        if ($fotoBaru !== null) {
            $data['foto_ktp'] = $fotoBaru;
        }

        $db = db_connect();
        $db->transStart();

        $tersimpan = $model->update((int) $id, $data);

        if ($status === 'aktif') {
            $this->ubahStatusKamar($kamarIdBaru, 'terisi');
        } elseif ($model->penghuniAktifPerKamar($kamarIdLama) === null) {
            $this->ubahStatusKamar($kamarIdLama, 'kosong');
        }

        $db->transComplete();

        if ($tersimpan === false || $db->transStatus() === false) {
            hapus_berkas($fotoBaru === null ? null : self::FOLDER_KTP . $fotoBaru);

            return redirect()->back()->withInput()
                ->with('errors', $model->errors() ?: ['umum' => 'Gagal memperbarui data penghuni.']);
        }

        if ($fotoBaru !== null) {
            hapus_berkas($penghuni['foto_ktp'] === null ? null : self::FOLDER_KTP . $penghuni['foto_ktp']);
        }

        return redirect()->to(site_url('admin/penghuni'))
            ->with('sukses', 'Data penghuni ' . $data['nama'] . ' berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $model    = new PenghuniModel();
        $penghuni = $model->find((int) $id);

        if ($penghuni === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Penghuni tidak ditemukan.');
        }

        $db = db_connect();
        $db->transStart();

        $model->delete((int) $id);

        if ($penghuni['status'] === 'aktif') {
            $this->ubahStatusKamar((int) $penghuni['kamar_id'], 'kosong');
        }

        $db->transComplete();

        hapus_berkas($penghuni['foto_ktp'] === null ? null : self::FOLDER_KTP . $penghuni['foto_ktp']);

        return redirect()->to(site_url('admin/penghuni'))
            ->with('sukses', 'Penghuni ' . $penghuni['nama'] . ' berhasil dihapus.');
    }

    /**
     * Akun role penghuni yang belum terhubung ke data penghuni,
     * ditambah akun yang sedang dipakai saat mode edit.
     */
    private function akunTersedia(?int $idDipakai = null): array
    {
        $db      = db_connect();
        $dipakai = array_column(
            $db->table('penghuni')->select('user_id')->get()->getResultArray(),
            'user_id'
        );

        $builder = $db->table('users')
            ->select('id, nama, username')
            ->where('role', 'penghuni');

        $builder->groupStart();
        if ($dipakai !== []) {
            $builder->whereNotIn('id', $dipakai);
        }
        if ($idDipakai !== null) {
            $builder->orWhere('id', $idDipakai);
        }
        $builder->groupEnd();

        return $builder->orderBy('nama', 'ASC')->get()->getResultArray();
    }

    private function kamarTersedia(?int $idDipakai = null): array
    {
        $db      = db_connect();
        $dipakai = array_column(
            $db->table('penghuni')->select('kamar_id')->where('status', 'aktif')->get()->getResultArray(),
            'kamar_id'
        );

        $builder = $db->table('kamar')
            ->select('id, nomor_kamar, tipe, harga_bulanan, status')
            ->where('status !=', 'perbaikan');

        if ($dipakai !== []) {
            $builder->whereNotIn('id', $dipakai);
        }
        if ($idDipakai !== null) {
            $builder->orWhere('id', $idDipakai);
        }

        return $builder->orderBy('nomor_kamar', 'ASC')->get()->getResultArray();
    }

    /**
     * Status kamar tidak diturunkan menjadi "kosong" jika kamar sedang diperbaiki.
     */
    private function ubahStatusKamar(int $kamarId, string $status): void
    {
        $kamarModel = new KamarModel();
        $kamar      = $kamarModel->find($kamarId);

        if ($kamar === null) {
            return;
        }

        if ($status === 'kosong' && $kamar['status'] === 'perbaikan') {
            return;
        }

        $kamarModel->update($kamarId, ['status' => $status]);
    }

    private function aturanValidasi(): array
    {
        return [
            'user_id'       => 'required|integer',
            'kamar_id'      => 'required|integer',
            'nama'          => 'required|min_length[3]|max_length[100]',
            'no_hp'         => 'permit_empty|max_length[20]',
            'tanggal_masuk' => 'required|valid_date[Y-m-d]',
            'status'        => 'required|in_list[aktif,keluar]',
        ];
    }

    private function pesanValidasi(): array
    {
        return [
            'user_id'       => ['required' => 'Akun login wajib dipilih.'],
            'kamar_id'      => ['required' => 'Kamar wajib dipilih.'],
            'nama'          => [
                'required'   => 'Nama penghuni wajib diisi.',
                'min_length' => 'Nama penghuni minimal 3 karakter.',
            ],
            'tanggal_masuk' => [
                'required'   => 'Tanggal masuk wajib diisi.',
                'valid_date' => 'Format tanggal masuk harus YYYY-MM-DD.',
            ],
            'status' => ['in_list' => 'Status penghuni tidak dikenal.'],
            'foto_ktp' => [
                'is_image' => 'Berkas KTP harus berupa gambar.',
                'mime_in'  => 'Format KTP harus JPG, JPEG, PNG, atau WEBP.',
                'max_size' => 'Ukuran KTP maksimal 2 MB.',
            ],
        ];
    }
}
