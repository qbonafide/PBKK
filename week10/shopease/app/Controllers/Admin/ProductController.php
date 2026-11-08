<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\OrderItemModel;
use App\Models\ProductModel;

/**
 * CRUD produk, upload gambar, dan penonaktifan produk yang sudah pernah dipesan.
 */
class ProductController extends BaseController
{
    private const PER_PAGE = 10;

    private const UPLOAD_RULES = [
        'image' => 'permit_empty|uploaded[image]|max_size[image,2048]'
            . '|ext_in[image,jpg,jpeg,png,webp]'
            . '|mime_in[image,image/jpeg,image/png,image/webp]|is_image[image]',
    ];

    public function index(): string
    {
        $productModel = new ProductModel();
        $keyword      = trim((string) $this->request->getGet('q'));

        $builder = $productModel->withCategory();

        if ($keyword !== '') {
            $builder->like('products.name', $keyword);
        }

        return view('admin/products/index', [
            'title'    => 'Produk',
            'section'  => 'Manajemen katalog',
            'products' => $builder->orderBy('products.id', 'DESC')->paginate(self::PER_PAGE),
            'pager'    => $productModel->pager,
            'keyword'  => $keyword,
        ]);
    }

    public function create(): string
    {
        return view('admin/products/form', [
            'title'      => 'Tambah Produk',
            'section'    => 'Manajemen katalog',
            'product'    => null,
            'categories' => (new CategoryModel())->orderBy('name', 'ASC')->findAll(),
            'action'     => site_url('admin/products'),
        ]);
    }

    public function store()
    {
        $input = $this->validatedInput();

        if ($input === null) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $image = $this->moveImage();

        if ($image === false) {
            return redirect()->back()->withInput()->with('error', 'Gambar gagal diunggah.');
        }

        (new ProductModel())->insert([
            'category_id' => $input['category_id'],
            'name'        => $input['name'],
            'slug'        => $this->uniqueSlug($input['name'], null),
            'description' => (string) $this->request->getPost('description'),
            'price'       => $input['price'],
            'stock'       => $input['stock'],
            'image'       => $image,
            'status'      => $input['status'],
        ]);

        return redirect()->to(site_url('admin/products'))->with('success', 'Produk baru ditambahkan.');
    }

    public function edit(int $id)
    {
        $product = (new ProductModel())->find($id);

        if ($product === null) {
            return redirect()->to(site_url('admin/products'))->with('error', 'Produk tidak ditemukan.');
        }

        return view('admin/products/form', [
            'title'      => 'Ubah Produk',
            'section'    => 'Manajemen katalog',
            'product'    => $product,
            'categories' => (new CategoryModel())->orderBy('name', 'ASC')->findAll(),
            'action'     => site_url('admin/products/' . $id),
        ]);
    }

    public function update(int $id)
    {
        $productModel = new ProductModel();
        $product      = $productModel->find($id);

        if ($product === null) {
            return redirect()->to(site_url('admin/products'))->with('error', 'Produk tidak ditemukan.');
        }

        $input = $this->validatedInput();

        if ($input === null) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $image = $this->moveImage();

        if ($image === false) {
            return redirect()->back()->withInput()->with('error', 'Gambar gagal diunggah.');
        }

        $data = [
            'category_id' => $input['category_id'],
            'name'        => $input['name'],
            'slug'        => $this->uniqueSlug($input['name'], $id),
            'description' => (string) $this->request->getPost('description'),
            'price'       => $input['price'],
            'stock'       => $input['stock'],
            'status'      => $input['status'],
        ];

        if (is_string($image)) {
            $data['image'] = $image;
        }

        $productModel->update($id, $data);

        return redirect()->to(site_url('admin/products'))->with('success', 'Produk diperbarui.');
    }

    /**
     * Hapus produk. Bila produk pernah dipesan, hanya dinonaktifkan
     * agar riwayat transaksi tetap utuh (BR-011).
     */
    public function delete(int $id)
    {
        $productModel = new ProductModel();
        $product      = $productModel->find($id);

        if ($product === null) {
            return redirect()->to(site_url('admin/products'))->with('error', 'Produk tidak ditemukan.');
        }

        if ((new OrderItemModel())->countByProduct($id) > 0) {
            $productModel->update($id, ['status' => 'inactive']);

            return redirect()->to(site_url('admin/products'))
                ->with('info', 'Produk pernah dipesan sehingga hanya dinonaktifkan, bukan dihapus.');
        }

        $productModel->delete($id);

        $path = FCPATH . 'uploads/products/' . (string) $product['image'];
        if ($product['image'] !== null && is_file($path)) {
            unlink($path);
        }

        return redirect()->to(site_url('admin/products'))->with('success', 'Produk dihapus.');
    }

    /**
     * Validasi input produk (dipakai store & update).
     *
     * @return array{category_id: int, name: string, price: float, stock: int, status: string}|null
     */
    private function validatedInput(): ?array
    {
        $rules = array_merge([
            'name'        => 'required|min_length[3]|max_length[150]',
            'category_id' => 'required|is_natural_no_zero|is_not_unique[categories.id]',
            'price'       => 'required|numeric|greater_than_equal_to[0]',
            'stock'       => 'required|is_natural',
            'status'      => 'required|in_list[active,inactive]',
        ], self::UPLOAD_RULES);

        if (! $this->validate($rules)) {
            return null;
        }

        return [
            'category_id' => (int) $this->request->getPost('category_id'),
            'name'        => trim((string) $this->request->getPost('name')),
            'price'       => (float) $this->request->getPost('price'),
            'stock'       => (int) $this->request->getPost('stock'),
            'status'      => (string) $this->request->getPost('status'),
        ];
    }

    /**
     * Pindahkan gambar produk ke public/uploads/products.
     *
     * @return string|false|null nama berkas, false bila gagal, null bila tidak ada unggahan
     */
    private function moveImage(): string|false|null
    {
        $file = $this->request->getFile('image');

        if ($file === null || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        $name = $file->getRandomName();

        if (! $file->move(FCPATH . 'uploads/products', $name)) {
            return false;
        }

        return $name;
    }

    private function uniqueSlug(string $name, ?int $ignoreId): string
    {
        $model = new ProductModel();
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
