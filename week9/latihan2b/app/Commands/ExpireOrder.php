<?php

namespace App\Commands;

use App\Models\OrderModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Command untuk membatalkan otomatis pesanan yang belum dibayar lebih dari 24 jam.
 * Stok produk otomatis dikembalikan oleh OrderModel::ubahStatus().
 *
 * Pemakaian: php spark order:expire
 */
class ExpireOrder extends BaseCommand
{
    protected $group       = 'ThriftIn';
    protected $name        = 'order:expire';
    protected $description = 'Membatalkan pesanan menunggu pembayaran yang sudah kedaluwarsa (lewat 24 jam).';

    public function run(array $params)
    {
        $model  = new OrderModel();
        $hasil  = $model->batalkanKedaluwarsa();

        if ($hasil['jumlah'] === 0) {
            CLI::write('Tidak ada pesanan kedaluwarsa.', 'yellow');

            return EXIT_SUCCESS;
        }

        CLI::write($hasil['jumlah'] . ' pesanan dibatalkan dan stoknya dikembalikan:', 'green');

        foreach ($hasil['invoice'] as $invoice) {
            CLI::write('  - ' . $invoice);
        }

        return EXIT_SUCCESS;
    }
}
