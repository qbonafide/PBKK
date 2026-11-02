<?php

/**
 * Helper berkas: menyimpan upload dengan nama acak dan menghapus berkas lama
 * supaya folder upload tidak menumpuk file yatim.
 */

if (! function_exists('simpan_upload')) {
    /**
     * Menyimpan file upload ke folder tujuan dengan nama acak.
     *
     * @param object|null $file   Instance UploadedFile dari request
     * @param string      $folder Folder absolut tujuan
     *
     * @return string|null Nama file yang tersimpan, atau null jika tidak ada file
     */
    function simpan_upload($file, string $folder, string $awalan = ''): ?string
    {
        if ($file === null || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        if (! is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $nama = $awalan . $file->getRandomName();
        $file->move($folder, $nama);

        return $nama;
    }
}

if (! function_exists('hapus_berkas')) {
    /**
     * Menghapus berkas lama dari disk. Aman dipanggil walau berkas tidak ada.
     */
    function hapus_berkas(?string $path): void
    {
        if ($path !== null && $path !== '' && is_file($path)) {
            unlink($path);
        }
    }
}
