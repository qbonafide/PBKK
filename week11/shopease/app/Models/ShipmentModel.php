<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model pengiriman: kurir, nomor resi, dan status pengiriman.
 */
class ShipmentModel extends Model
{
    protected $table            = 'shipments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'order_id', 'courier', 'tracking_number', 'shipment_status', 'shipped_at', 'delivered_at',
    ];

    protected $validationRules = [
        'courier'         => 'required|max_length[100]',
        'tracking_number' => 'required|max_length[100]',
    ];

    /**
     * Data pengiriman untuk satu pesanan.
     */
    public function forOrder(int $orderId): ?array
    {
        return $this->where('order_id', $orderId)->first();
    }

    /**
     * Simpan atau perbarui data pengiriman satu pesanan.
     */
    public function saveForOrder(int $orderId, array $data): bool
    {
        $existing = $this->forOrder($orderId);

        if ($existing === null) {
            return (bool) $this->insert(array_merge(['order_id' => $orderId], $data));
        }

        return (bool) $this->update($existing['id'], $data);
    }
}
