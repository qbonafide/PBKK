<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;

/**
 * CRUD kategori (khusus admin).
 */
class CategoryController extends BaseController
{
    private const PER_PAGE = 10;

    public function index(): string
    {
        $categoryModel = new CategoryModel();
        $categories    = $categoryModel
            ->select('categories.*, COUNT(products.id) AS product_count')
            ->join('products', 'products.category_id = categories.id', 'left')
            ->groupBy('categories.id')
            ->orderBy('categories.name', 'ASC')
            ->paginate(self::PER_PAGE);

        return view('admin/categories/index', [
            'title'      => 'Kategori',
            'section'    => 'Manajemen katalog',
            'categories' => $categories,
            'pager'      => $categoryModel->pager,
        ]);
    }

    public function create(): string
    {
        return view('admin/categories/form', [
            'title'    => 'Tambah Kategori',
            'section'  => 'Manajemen katalog',
            'category' => null,
            'action'   => site_url('admin/categories'),
        ]);
    }

    public function store()
    {
        $rules = [
            'name'        => 'required|min_length[3]|max_length[100]',
            'description' => 'permit_empty',
            'is_active'   => 'required|in_list[0,1]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name = (string) $this->request->getPost('name');

        (new CategoryModel())->insert([
            'name'        => $name,
            'slug'        => $this->uniqueSlug($name, null),
            'description' => (string) ($this->request->getPost('description') ?? ''),
            'is_active'   => (int) $this->request->getPost('is_active'),
        ]);

        return redirect()->to(site_url('admin/categories'))->with('success', 'Kategori baru ditambahkan.');
    }

    public function edit(int $id)
    {
        $category = (new CategoryModel())->find($id);

        if ($category === null) {
            return redirect()->to(site_url('admin/categories'))->with('error', 'Kategori tidak ditemukan.');
        }

        return view('admin/categories/form', [
            'title'    => 'Ubah Kategori',
            'section'  => 'Manajemen katalog',
            'category' => $category,
            'action'   => site_url('admin/categories/' . $id),
        ]);
    }

    public function update(int $id)
    {
        $categoryModel = new CategoryModel();
        $category      = $categoryModel->find($id);

        if ($category === null) {
            return redirect()->to(site_url('admin/categories'))->with('error', 'Kategori tidak ditemukan.');
        }

        $rules = [
            'name'        => 'required|min_length[3]|max_length[100]',
            'description' => 'permit_empty',
            'is_active'   => 'required|in_list[0,1]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name = (string) $this->request->getPost('name');

        $categoryModel->update($id, [
            'name'        => $name,
            'slug'        => $this->uniqueSlug($name, $id),
            'description' => (string) ($this->request->getPost('description') ?? ''),
            'is_active'   => (int) $this->request->getPost('is_active'),
        ]);

        return redirect()->to(site_url('admin/categories'))->with('success', 'Kategori diperbarui.');
    }

    /**
     * Hapus kategori. Ditolak bila masih dipakai produk (FR-005).
     */
    public function delete(int $id)
    {
        $categoryModel = new CategoryModel();

        if ($categoryModel->find($id) === null) {
            return redirect()->to(site_url('admin/categories'))->with('error', 'Kategori tidak ditemukan.');
        }

        if ($categoryModel->hasProducts($id)) {
            return redirect()->to(site_url('admin/categories'))
                ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki produk. Pindahkan produk terlebih dahulu.');
        }

        $categoryModel->delete($id);

        return redirect()->to(site_url('admin/categories'))->with('success', 'Kategori dihapus.');
    }

    /**
     * Buat slug unik dari nama kategori.
     */
    private function uniqueSlug(string $name, ?int $ignoreId): string
    {
        $model = new CategoryModel();
        $base  = url_title($name, '-', true);
        $slug  = $base;
        $i     = 2;

        while (true) {
            $existing = $model->where('slug', $slug)->first();

            if ($existing === null || (int) $existing['id'] === $ignoreId) {
                return $slug;
            }

            $slug = $base . '-' . $i;
            $i++;
        }
    }
}
