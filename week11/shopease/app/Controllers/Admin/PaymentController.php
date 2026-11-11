<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\PaymentModel;

/**
 * Verifikasi pembayaran manual.
 *
 * approve : payment -> verified, order -> processing
 * reject  : payment -> rejected, order -> pending_payment (customer boleh unggah ulang)
 * Keduanya memvalidasi status awal agar tidak terjadi verifikasi ganda.
 */
class PaymentController extends BaseController
{
    private const PER_PAGE = 10;

    public function index(): string
    {
        $paymentModel = new PaymentModel();
        $status       = (string) ($this->request->getGet('status') ?? '');

        $payments = $status === PaymentModel::STATUS_SUBMITTED || $status === ''
            ? $paymentModel->waitingVerification(self::PER_PAGE)
            : $paymentModel->listByStatus($status, self::PER_PAGE);

        return view('admin/payments/index', [
            'title'    => 'Verifikasi Pembayaran',
            'section'  => 'Operasional',
            'payments' => $payments,
            'pager'    => $paymentModel->pager,
            'status'   => $status,
        ]);
    }

    public function approve(int $id)
    {
        $paymentModel = new PaymentModel();
        $payment      = $paymentModel->find($id);

        if ($payment === null) {
            return redirect()->to(site_url('admin/payments'))->with('error', 'Data pembayaran tidak ditemukan.');
        }

        if ($payment['payment_status'] !== PaymentModel::STATUS_SUBMITTED) {
            return redirect()->back()->with('error', 'Hanya pembayaran berstatus menunggu verifikasi yang dapat disetujui.');
        }

        $orderModel = new OrderModel();
        $order      = $orderModel->find((int) $payment['order_id']);

        if ($order === null) {
            return redirect()->to(site_url('admin/payments'))->with('error', 'Pesanan terkait tidak ditemukan.');
        }

        if (! OrderModel::canTransition($order['order_status'], OrderModel::STATUS_PROCESSING)) {
            return redirect()->back()->with('error', 'Status pesanan tidak memungkinkan untuk disetujui saat ini.');
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $paymentModel->update($id, [
                'payment_status' => PaymentModel::STATUS_VERIFIED,
                'verified_by'    => $this->userId(),
                'verified_at'    => date('Y-m-d H:i:s'),
                'notes'          => null,
            ]);

            $orderModel->update((int) $order['id'], ['order_status' => OrderModel::STATUS_PROCESSING]);

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Gagal menyimpan verifikasi.');
            }

            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();

            return redirect()->back()->with('error', 'Verifikasi gagal disimpan: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/payments'))
            ->with('success', 'Pembayaran ' . $order['order_number'] . ' disetujui dan pesanan masuk proses.');
    }

    public function reject(int $id)
    {
        $paymentModel = new PaymentModel();
        $payment      = $paymentModel->find($id);

        if ($payment === null) {
            return redirect()->to(site_url('admin/payments'))->with('error', 'Data pembayaran tidak ditemukan.');
        }

        if ($payment['payment_status'] !== PaymentModel::STATUS_SUBMITTED) {
            return redirect()->back()->with('error', 'Hanya pembayaran berstatus menunggu verifikasi yang dapat ditolak.');
        }

        $reason = trim((string) $this->request->getPost('notes'));

        if ($reason === '') {
            return redirect()->back()->with('error', 'Tuliskan alasan penolakan agar customer tahu tindak lanjutnya.');
        }

        $orderModel = new OrderModel();
        $order      = $orderModel->find((int) $payment['order_id']);

        if ($order === null) {
            return redirect()->to(site_url('admin/payments'))->with('error', 'Pesanan terkait tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $paymentModel->update($id, [
                'payment_status' => PaymentModel::STATUS_REJECTED,
                'verified_by'    => $this->userId(),
                'verified_at'    => date('Y-m-d H:i:s'),
                'notes'          => $reason,
            ]);

            if (OrderModel::canTransition($order['order_status'], OrderModel::STATUS_PENDING)) {
                $orderModel->update((int) $order['id'], ['order_status' => OrderModel::STATUS_PENDING]);
            }

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Gagal menyimpan penolakan.');
            }

            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();

            return redirect()->back()->with('error', 'Penolakan gagal disimpan: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/payments'))
            ->with('success', 'Bukti pembayaran ditolak. Customer dapat mengunggah ulang.');
    }
}
