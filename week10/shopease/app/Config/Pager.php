<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Pager extends BaseConfig
{
    /**
     * Template pagination. `shopease` memakai markup Bootstrap 5
     * (lihat app/Views/partials/pager.php) agar tampilan seragam.
     *
     * @var array<string, string>
     */
    public array $templates = [
        'default_full'   => 'CodeIgniter\Pager\Views\default_full',
        'default_simple' => 'CodeIgniter\Pager\Views\default_simple',
        'default_head'   => 'CodeIgniter\Pager\Views\default_head',
        'shopease'       => 'App\Views\partials\pager',
    ];

    /**
     * Jumlah data per halaman bila paginate() dipanggil tanpa argumen.
     */
    public int $perPage = 10;
}
