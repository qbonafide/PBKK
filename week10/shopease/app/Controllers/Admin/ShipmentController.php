<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\ShipmentModel;

/**
 * Pengiriman: simpan kurir + nomor resi, lalu ubah pesanan menjadi `shipped`.
 */
class ShipmentController extends BaseController
{
    public function store(int $orderId)
    {
        $orderModel = new OrderModel();
        $order      = $orderModel->find($orderId);

        if ($order === null) {
            return redirect()->to(site_url('admin/orders'))->with('error', 'Pesanan tidak ditemukan.');
        }

        $rules = [
            'courier'         => 'required|min_length[2]|max_length[100]',
            'tracking_number' => 'required|min_length[4]|max_length[100]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to(site_url('admin/orders/' . $orderId))
                ->with('errors', $this->validator->getErrors());
        }

        $shipmentModel = new ShipmentModel();
        $existing      = $shipmentModel->forOrder($orderId);

        $isFirstShipment = $existing === null;

        if ($isFirstShipment && ! OrderModel::canTransition($order['order_status'], OrderModel::STATUS_SHIPPED)) {
            return redirect()->to(site_url('admin/orders/' . $orderId))
                ->with('error', 'Pesanan harus berstatus "Diproses" sebelum dapat dikirim.');
        }

        $data = [
            'courier'         => (string) $this->request->getPost('courier'),
            'tracking_number' => (string) $this->request->getPost('tracking_number'),
            'shipment_status' => 'shipped',
        ];

        if ($isFirstShipment) {
            $data['shipped_at'] = date('Y-m-d H:i:s');
        }

        $shipmentModel->saveForOrder($orderId, $data);

        if ($isFirstShipment) {
            $orderModel->update($orderId, ['order_status' => OrderModel::STATUS_SHIPPED]);
        }

        return redirect()->to(site_url('admin/orders/' . $orderId))
            ->with('success', 'Data pengiriman disimpan. Pesanan ditandai sebagai dikirim.');
    }
}
