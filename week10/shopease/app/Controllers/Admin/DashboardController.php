<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\PaymentModel;
use App\Models\ProductModel;
use App\Models\UserModel;

/**
 * Dashboard admin/operator: kartu statistik, grafik penjualan,
 * distribusi status, produk terlaris, dan transaksi terbaru.
 */
class DashboardController extends BaseController
{
    public function index(): string
    {
        $productModel = new ProductModel();
        $userModel    = new UserModel();
        $orderModel   = new OrderModel();
        $paymentModel = new PaymentModel();

        $pendingOrders = $orderModel->whereIn('order_status', [
            OrderModel::STATUS_PENDING,
            OrderModel::STATUS_WAITING,
        ])->countAllResults();

        $recentOrders = $orderModel
            ->select('orders.*, users.name AS customer_name')
            ->join('users', 'users.id = orders.user_id')
            ->orderBy('orders.order_date', 'DESC')
            ->findAll(5);

        return view('admin/dashboard', [
            'title'  => 'Dashboard',
            'section' => 'Ringkasan toko',
            'stats'  => [
                'products'         => $productModel->countAllResults(),
                'active_products'  => $productModel->where('status', 'active')->countAllResults(),
                'customers'        => $userModel->where('role', 'customer')->countAllResults(),
                'orders'           => $orderModel->countAllResults(),
                'sales'            => $orderModel->totalSales(),
                'pending_orders'   => $pendingOrders,
                'waiting_payment'  => $paymentModel->countWaiting(),
            ],
            'salesSeries'    => $this->buildDailySeries($orderModel->dailySales(14)),
            'statusBreakdown' => $orderModel->statusDistribution(),
            'bestSellers'    => $productModel->bestSellers(5),
            'recentOrders'   => $recentOrders,
        ]);
    }

    /**
     * Melengkapi rentang 14 hari terakhir agar grafik tetap kontinu
     * walaupun ada hari tanpa transaksi.
     *
     * @param list<array{tanggal: string, penjualan: string, jumlah: string}> $rows
     *
     * @return array{labels: list<string>, sales: list<float>, orders: list<int>}
     */
    private function buildDailySeries(array $rows): array
    {
        $byDate = [];
        foreach ($rows as $row) {
            $byDate[$row['tanggal']] = [
                'penjualan' => (float) $row['penjualan'],
                'jumlah'    => (int) $row['jumlah'],
            ];
        }

        $labels = [];
        $sales  = [];
        $orders = [];

        for ($i = 13; $i >= 0; $i--) {
            $date     = date('Y-m-d', strtotime('-' . $i . ' days'));
            $labels[] = date('d M', strtotime($date));
            $sales[]  = $byDate[$date]['penjualan'] ?? 0.0;
            $orders[] = $byDate[$date]['jumlah'] ?? 0;
        }

        return ['labels' => $labels, 'sales' => $sales, 'orders' => $orders];
    }
}
