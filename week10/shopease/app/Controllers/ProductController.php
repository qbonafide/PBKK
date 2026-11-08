<?php

namespace App\Controllers;

use App\Models\ProductModel;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Katalog publik dan detail produk.
 */
class ProductController extends BaseController
{
    private const PER_PAGE = 8;

    /**
     * Katalog dengan pencarian nama, filter kategori, pengurutan harga,
     * dan pagination 8 item per halaman.
     */
    public function catalog(): string
    {
        $filters = [
            'q'        => trim((string) $this->request->getGet('q')),
            'category' => (int) ($this->request->getGet('category') ?? 0),
            'sort'     => (string) ($this->request->getGet('sort') ?? ''),
        ];

        $productModel = new ProductModel();
        $products     = $productModel->catalog($filters, self::PER_PAGE);

        return view('frontend/catalog', [
            'title'      => 'Katalog Produk',
            'products'   => $products,
            'pager'      => $productModel->pager,
            'categories' => (new \App\Models\CategoryModel())->withProductCount(),
            'filters'    => $filters,
            'total'      => $productModel->pager->getTotal(),
        ]);
    }

    /**
     * Detail produk aktif. Produk nonaktif dianggap tidak ada (BR-002).
     */
    public function detail(int $id): string
    {
        $productModel = new ProductModel();
        $product      = $productModel->withCategory()
            ->where('products.status', 'active')
            ->where('products.id', $id)
            ->first();

        if ($product === null) {
            throw PageNotFoundException::forPageNotFound('Produk tidak ditemukan atau sudah tidak dijual.');
        }

        $related = $productModel->withCategory()
            ->where('products.status', 'active')
            ->where('products.category_id', $product['category_id'])
            ->where('products.id !=', $id)
            ->orderBy('products.created_at', 'DESC')
            ->findAll(4);

        return view('frontend/product_detail', [
            'title'   => $product['name'],
            'product' => $product,
            'related' => $related,
        ]);
    }
}
