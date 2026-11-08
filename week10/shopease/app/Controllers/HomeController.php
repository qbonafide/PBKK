<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;

/**
 * Halaman publik: beranda, tentang, dan kontak.
 */
class HomeController extends BaseController
{
    public function index(): string
    {
        $productModel  = new ProductModel();
        $categoryModel = new CategoryModel();

        return view('frontend/home', [
            'title'       => 'Beranda',
            'categories'  => $categoryModel->withProductCount(),
            'latest'      => $productModel->latestActive(8),
            'bestSellers' => $productModel->bestSellers(4),
        ]);
    }

    public function about(): string
    {
        return view('frontend/about', [
            'title' => 'Tentang ShopEase',
        ]);
    }

    public function contact(): string
    {
        return view('frontend/contact', [
            'title' => 'Kontak',
        ]);
    }
}
