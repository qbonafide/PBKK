<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\PaymentModel;
use App\Models\ShipmentModel;

/**
 * Pemrosesan pesanan oleh admin/operator.
 *
 * Perpindahan status selalu divalidasi lewat OrderModel::canTransition()
 * sehingga tombol yang "tidak seharusnya" tetap ditolak di backend.
 */
class OrderController extends BaseController
{
    private const PER_PAGE = 10;

    public function index(): string
    {
        $orderModel = new OrderModel();

        $filters = [
            'status' => (string) ($this->request->getGet('status') ?? ''),
            'q'      => trim((string) ($this->request->getGet('q') ?? '')),
        ];

        return view('admin/orders/index', [
            'title'   => 'Pesanan',
            'section' => 'Operasional',
            'orders'  => $orderModel->adminList($filters, self::PER_PAGE),
            'pager'   => $orderModel->pager,
            'filters' => $filters,
        ]);
    }

    public function detail(int $id)
    {
        $orderModel = new OrderModel();
        $order      = $orderModel->detail($id);

        if ($order === null) {
            return redirect()->to(site_url('admin/orders'))->with('error', 'Pesanan tidak ditemukan.');
        }

        return view('admin/orders/detail', [
            'title'    => 'Pesanan ' . $order['order_number'],
            'section'  => 'Operasional',
            'order'    => $order,
            'items'    => (new OrderItemModel())->forOrder($id),
            'payment'  => (new PaymentModel())->forOrder($id),
            'shipment' => (new ShipmentModel())->forOrder($id),
        ]);
    }

    /**
     * Ubah status pesanan (dibatalkan / selesai) dengan validasi transisi.
     */
    public function updateStatus(int $id)
    {
        $orderModel = new OrderModel();
        $order      = $orderModel->find($id);

        if ($order === null) {
            return redirect()->to(site_url('admin/orders'))->with('error', 'Pesanan tidak ditemukan.');
        }

        $target = (string) $this->request->getPost('order_status');

        if (! in_array($target, OrderModel::statuses(), true)) {
            return redirect()->back()->with('error', 'Status pesanan tidak dikenal.');
        }

        if (! OrderModel::canTransition($order['order_status'], $target)) {
            return redirect()->back()->with(
                'error',
                'Perubahan status dari "' . order_status_label($order['order_status']) . '" ke "'
                . order_status_label($target) . '" tidak diizinkan.'
            );
        }

        $orderModel->update($id, ['order_status' => $target]);

        // Saat pesanan selesai, catat juga penyelesaian pengiriman.
        if ($target === OrderModel::STATUS_COMPLETED) {
            $shipmentModel = new ShipmentModel();
            $shipment      = $shipmentModel->forOrder($id);

            if ($shipment !== null) {
                $shipmentModel->update((int) $shipment['id'], [
                    'shipment_status' => 'delivered',
                    'delivered_at'    => date('Y-m-d H:i:s'),
                ]);
            }
        }

        return redirect()->to(site_url('admin/orders/' . $id))
            ->with('success', 'Status pesanan diperbarui menjadi "' . order_status_label($target) . '".');
    }
}
