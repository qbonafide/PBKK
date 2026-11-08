<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;

/**
 * Laporan penjualan dengan filter periode dan status.
 */
class ReportController extends BaseController
{
    private const PER_PAGE = 15;

    public function index(): string
    {
        $start  = (string) ($this->request->getGet('start') ?? date('Y-m-01'));
        $end    = (string) ($this->request->getGet('end') ?? date('Y-m-t'));
        $status = (string) ($this->request->getGet('status') ?? '');

        // Validasi sederhana format tanggal agar query tetap aman dan masuk akal.
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $start)) {
            $start = date('Y-m-01');
        }
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $end)) {
            $end = date('Y-m-t');
        }
        if ($start > $end) {
            [$start, $end] = [$end, $start];
        }

        $orderModel = new OrderModel();

        return view('admin/reports/index', [
            'title'   => 'Laporan Penjualan',
            'section' => 'Analitik',
            'orders'  => $orderModel->report($start, $end, $status, self::PER_PAGE),
            'pager'   => $orderModel->pager,
            'filters' => ['start' => $start, 'end' => $end, 'status' => $status],
            'summary' => $orderModel->summaryForPeriod($start, $end),
            'statuses' => OrderModel::statuses(),
        ]);
    }
}
