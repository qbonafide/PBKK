<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController: tempat bersama untuk semua controller ShopEase.
 *
 * Yang dibagikan di sini sengaja dijaga seminimal mungkin:
 * - helper `form` (csrf_field), `url` (base_url/url_to), dan `shopease` (rupiah, badge)
 * - akses singkat ke service session
 */
abstract class BaseController extends Controller
{
    /**
     * Helper yang selalu tersedia di controller dan view.
     * Harus diisi sebelum parent::initController() dipanggil.
     *
     * @var list<string>
     */
    protected $helpers = ['form', 'url', 'shopease'];

    protected $session;

    /**
     * @param CLIRequest|IncomingRequest $request
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->session = service('session');
    }

    /**
     * Id pengguna yang sedang login (0 bila tamu).
     */
    protected function userId(): int
    {
        return (int) $this->session->get('user_id');
    }

    /**
     * Halaman 403 dengan layout minimal, dipakai saat akses ditolak di level data
     * (mis. customer membuka pesanan milik customer lain).
     */
    protected function forbidden(string $message = 'Anda tidak memiliki izin untuk membuka data ini.'): ResponseInterface
    {
        return $this->response
            ->setStatusCode(ResponseInterface::HTTP_FORBIDDEN)
            ->setBody(view('errors/html/error_403', [
                'title'   => 'Akses Ditolak',
                'message' => $message,
            ]));
    }
}
