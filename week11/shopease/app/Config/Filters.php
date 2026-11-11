<?php

namespace Config;

use App\Filters\AuthFilter;
use App\Filters\GuestFilter;
use CodeIgniter\Config\Filters as BaseFilters;
use CodeIgniter\Filters\Cors;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\PageCache;
use CodeIgniter\Filters\PerformanceMetrics;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseFilters
{
    /**
     * Alias filter agar route cukup menulis nama pendek.
     *
     * @var array<string, class-string|list<class-string>>
     */
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'cors'          => Cors::class,
        'forcehttps'    => ForceHTTPS::class,
        'pagecache'     => PageCache::class,
        'performance'   => PerformanceMetrics::class,
        // Filter buatan aplikasi
        'auth'          => AuthFilter::class,
        'guest'         => GuestFilter::class,
    ];

    /**
     * Filter wajib bawaan framework.
     *
     * @var array{before: list<string>, after: list<string>}
     */
    public array $required = [
        'before' => [
            'forcehttps', // Force Global Secure Requests
            'pagecache',  // Web Page Caching
        ],
        'after' => [
            'pagecache',   // Web Page Caching
            'performance', // Performance Metrics
            'toolbar',     // Debug Toolbar
        ],
    ];

    /**
     * Filter global.
     *
     * CSRF dijalankan untuk SEMUA request sehingga setiap POST tanpa token
     * (termasuk hapus data & ubah status) otomatis ditolak.
     *
     * @var array{
     *     before: array<string, array{except: list<string>|string}>|list<string>,
     *     after: array<string, array{except: list<string>|string}>|list<string>
     * }
     */
    public array $globals = [
        'before' => [
            'csrf',
        ],
        'after' => [
            // 'secureheaders',
        ],
    ];

    /**
     * Filter berdasarkan metode HTTP.
     *
     * @var array<string, list<string>>
     */
    public array $methods = [];

    /**
     * Filter berbasis pola URI.
     *
     * @var array<string, array<string, list<string>>>
     */
    public array $filters = [];
}
