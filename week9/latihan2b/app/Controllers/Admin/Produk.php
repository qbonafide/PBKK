<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KategoriModel;
use App\Models\ProdukModel;

/**
 * CRUD produk. Foto produk boleh publik (disimpan di public/uploads/produk),
 * nama file acak, dan foto lama dihapus saat diganti atau produk dihapus.
 */
class Produk extends BaseController
{
    private const FOLDER_FOTO = FCPATH . 'uploads/produk/';

    public function index()
    {
        $model   = new ProdukModel();
        $keyword = (string) ($this->request->getGet('q') ?? '');

        $model->select('produk.*, kategori.nama AS nama_kategori')
            ->join('kategori', 'kategori.id = produk.kategori_id', 'left');

        if ($keyword !== '') {
            $model->groupStart()
                ->like('produk.nama', $keyword)
                ->orLike('produk.brand', $keyword)
                ->groupEnd();
        }

        service('pager')->only(['q']);

        return view('admin/produk/index', [
            'title'   => 'Kelola Produk',
            'produk'  => $model->orderBy('produk.created_at', 'DESC')->paginate(10),
            'pager'   => $model->pager,
            'keyword' => $keyword,
        ]);
    }

    public function create()
    {
        return view('admin/produk/form', [
            'title'    => 'Tambah Produk',
            'produk'   => null,
            'kategori' => (new KategoriModel())->orderBy('nama', 'ASC')->findAll(),
        ]);
    }

    public function store()
    {
        $aturan = $this->aturanValidasi();

        if (! $this->validate($aturan, $this->pesanValidasi())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = new ProdukModel();
        $nama  = (string) $this->request->getPost('nama');
        $foto  = simpan_upload($this->request->getFile('foto'), self::FOLDER_FOTO);

        $tersimpan = $model->insert([
            'kategori_id' => (int) $this->request->getPost('kategori_id'),
            'nama'        => $nama,
            'slug'        => $model->buatSlug($nama),
            'brand'       => (string) $this->request->getPost('brand'),
            'ukuran'      => (string) $this->request->getPost('ukuran'),
            'kondisi'     => (string) $this->request->getPost('kondisi'),
            'deskripsi'   => (string) $this->request->getPost('deskripsi'),
            'harga'       => (int) $this->request->getPost('harga'),
            'stok'        => (int) $this->request->getPost('stok'),
            'foto'        => $foto,
            'aktif'       => $this->request->getPost('aktif') === '1' ? 1 : 0,
        ]);

        if ($tersimpan === false) {
            hapus_berkas($foto === null ? null : self::FOLDER_FOTO . $foto);

            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        return redirect()->to(site_url('admin/produk'))->with('sukses', 'Produk berhasil ditambahkan.');
    }

    public function edit($id = null)
    {
        $produk = (new ProdukModel())->find((int) $id);

        if ($produk === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Produk tidak ditemukan.');
        }

        return view('admin/produk/form', [
            'title'    => 'Edit Produk',
            'produk'   => $produk,
            'kategori' => (new KategoriModel())->orderBy('nama', 'ASC')->findAll(),
        ]);
    }

    public function update($id = null)
    {
        $model  = new ProdukModel();
        $produk = $model->find((int) $id);

        if ($produk === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Produk tidak ditemukan.');
        }

        if (! $this->validate($this->aturanValidasi(), $this->pesanValidasi())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nama     = (string) $this->request->getPost('nama');
        $fotoBaru = simpan_upload($this->request->getFile('foto'), self::FOLDER_FOTO);

        $data = [
            'kategori_id' => (int) $this->request->getPost('kategori_id'),
            'nama'        => $nama,
            'slug'        => $model->buatSlug($nama, (int) $id),
            'brand'       => (string) $this->request->getPost('brand'),
            'ukuran'      => (string) $this->request->getPost('ukuran'),
            'kondisi'     => (string) $this->request->getPost('kondisi'),
            'deskripsi'   => (string) $this->request->getPost('deskripsi'),
            'harga'       => (int) $this->request->getPost('harga'),
            'stok'        => (int) $this->request->getPost('stok'),
            'aktif'       => $this->request->getPost('aktif') === '1' ? 1 : 0,
        ];

        if ($fotoBaru !== null) {
            $data['foto'] = $fotoBaru;
        }

        if ($model->update((int) $id, $data) === false) {
            hapus_berkas($fotoBaru === null ? null : self::FOLDER_FOTO . $fotoBaru);

            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        if ($fotoBaru !== null) {
            hapus_berkas($produk['foto'] === null ? null : self::FOLDER_FOTO . $produk['foto']);
        }

        return redirect()->to(site_url('admin/produk'))->with('sukses', 'Produk berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $model  = new ProdukModel();
        $produk = $model->find((int) $id);

        if ($produk === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Produk tidak ditemukan.');
        }

        $db = db_connect();

        // Produk yang sudah pernah dipesan tetap disimpan riwayatnya, jadi
        // penghapusan hanya menonaktifkan produk.
        $pernahDipesan = $db->table('order_items')->where('produk_id', (int) $id)->countAllResults() > 0;

        if ($pernahDipesan) {
            $model->update((int) $id, ['aktif' => 0]);

            return redirect()->to(site_url('admin/produk'))
                ->with('sukses', 'Produk sudah pernah dipesan, jadi dinonaktifkan (riwayat pesanan tetap aman).');
        }

        $model->delete((int) $id);
        hapus_berkas($produk['foto'] === null ? null : self::FOLDER_FOTO . $produk['foto']);

        return redirect()->to(site_url('admin/produk'))->with('sukses', 'Produk berhasil dihapus.');
    }

    private function aturanValidasi(): array
    {
        return [
            'kategori_id' => 'required|integer',
            'nama'        => 'required|min_length[3]|max_length[120]',
            'ukuran'      => 'permit_empty|max_length[20]',
            'kondisi'     => 'required|in_list[like_new,bagus,ada_minus]',
            'harga'       => 'required|integer|greater_than[0]',
            'stok'        => 'required|integer|greater_than_equal_to[0]',
            'foto'        => 'permit_empty|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png,image/webp]|max_size[foto,2048]',
        ];
    }

    private function pesanValidasi(): array
    {
        return [
            'kategori_id' => ['required' => 'Kategori wajib dipilih.'],
            'nama'        => [
                'required'   => 'Nama produk wajib diisi.',
                'min_length' => 'Nama produk minimal 3 karakter.',
            ],
            'kondisi'     => ['in_list' => 'Kondisi tidak dikenal.'],
            'harga'       => [
                'required'     => 'Harga wajib diisi.',
                'integer'      => 'Harga harus berupa angka.',
                'greater_than' => 'Harga harus lebih besar dari nol.',
            ],
            'stok'        => [
                'required'              => 'Stok wajib diisi.',
                'integer'               => 'Stok harus berupa angka.',
                'greater_than_equal_to' => 'Stok tidak boleh negatif.',
            ],
            'foto'        => [
                'is_image' => 'Berkas harus berupa gambar.',
                'mime_in'  => 'Format gambar harus JPG, JPEG, PNG, atau WEBP.',
                'max_size' => 'Ukuran gambar maksimal 2 MB.',
            ],
        ];
    }
}
