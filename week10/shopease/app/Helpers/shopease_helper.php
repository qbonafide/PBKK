<?php

/**
 * Helper tampilan ShopEase.
 *
 * Berisi fungsi kecil yang dipakai berulang di view: format Rupiah,
 * label status, badge, dan URL gambar produk.
 */

if (! function_exists('rupiah')) {
    /**
     * Format angka menjadi mata uang Rupiah tanpa desimal.
     */
    function rupiah(float|int|string|null $value): string
    {
        return 'Rp ' . number_format((float) ($value ?? 0), 0, ',', '.');
    }
}

if (! function_exists('current_user')) {
    /**
     * Data pengguna yang sedang login dari session (null bila tamu).
     */
    function current_user(): ?array
    {
        if (! session()->get('logged_in')) {
            return null;
        }

        return [
            'id'    => (int) session()->get('user_id'),
            'name'  => (string) session()->get('name'),
            'role'  => (string) session()->get('role'),
            'email' => (string) session()->get('email'),
        ];
    }
}

if (! function_exists('has_role')) {
    /**
     * Cek apakah pengguna yang login memiliki salah satu role.
     */
    function has_role(string ...$roles): bool
    {
        return in_array((string) session()->get('role'), $roles, true);
    }
}

if (! function_exists('order_status_label')) {
    /**
     * Label Bahasa Indonesia untuk status pesanan.
     */
    function order_status_label(string $status): string
    {
        return [
            'pending_payment'      => 'Menunggu Pembayaran',
            'waiting_verification' => 'Menunggu Verifikasi',
            'processing'           => 'Diproses',
            'shipped'              => 'Dikirim',
            'completed'            => 'Selesai',
            'cancelled'            => 'Dibatalkan',
        ][$status] ?? ucfirst(str_replace('_', ' ', $status));
    }
}

if (! function_exists('order_status_badge')) {
    /**
     * Badge Bootstrap untuk status pesanan.
     * Nilai berasal dari daftar tetap sehingga aman ditampilkan tanpa esc().
     */
    function order_status_badge(string $status): string
    {
        $class = [
            'pending_payment'      => 'text-bg-secondary',
            'waiting_verification' => 'text-bg-warning',
            'processing'           => 'text-bg-info',
            'shipped'              => 'text-bg-primary',
            'completed'            => 'text-bg-success',
            'cancelled'            => 'text-bg-danger',
        ][$status] ?? 'text-bg-secondary';

        return '<span class="badge ' . $class . '">' . esc(order_status_label($status)) . '</span>';
    }
}

if (! function_exists('payment_status_label')) {
    /**
     * Label Bahasa Indonesia untuk status pembayaran.
     */
    function payment_status_label(string $status): string
    {
        return [
            'unpaid'    => 'Belum Dibayar',
            'submitted' => 'Menunggu Verifikasi',
            'verified'  => 'Terverifikasi',
            'rejected'  => 'Ditolak',
        ][$status] ?? ucfirst($status);
    }
}

if (! function_exists('payment_status_badge')) {
    /**
     * Badge Bootstrap untuk status pembayaran.
     */
    function payment_status_badge(string $status): string
    {
        $class = [
            'unpaid'    => 'text-bg-secondary',
            'submitted' => 'text-bg-warning',
            'verified'  => 'text-bg-success',
            'rejected'  => 'text-bg-danger',
        ][$status] ?? 'text-bg-secondary';

        return '<span class="badge ' . $class . '">' . esc(payment_status_label($status)) . '</span>';
    }
}

if (! function_exists('shipment_status_label')) {
    /**
     * Label Bahasa Indonesia untuk status pengiriman.
     */
    function shipment_status_label(?string $status): string
    {
        return [
            'pending'   => 'Belum Dikirim',
            'shipped'   => 'Dalam Pengiriman',
            'delivered' => 'Diterima',
        ][(string) $status] ?? 'Belum Dikirim';
    }
}

if (! function_exists('product_image')) {
    /**
     * URL gambar produk; jatuh ke gambar placeholder bila file tidak ada.
     */
    function product_image(?string $image): string
    {
        if ($image !== null && $image !== '' && is_file(FCPATH . 'uploads/products/' . $image)) {
            return base_url('uploads/products/' . $image);
        }

        return base_url('assets/img/product-placeholder.svg');
    }
}
