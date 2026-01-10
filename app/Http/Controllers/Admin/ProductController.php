<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ImageCompressionService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected $imageCompressionService;

    public function __construct(ImageCompressionService $imageCompressionService)
    {
        $this->imageCompressionService = $imageCompressionService;
    }

    public function index()
    {
        $query = Product::with('category', 'images');

        if (request('search')) {
            $query->where('name', 'like', '%' . request('search') . '%');
        }

        if (request('filter')) {
            $filter = request('filter');
            if ($filter === 'instock') {
                $query->where('stock', '>', 0);
            } elseif ($filter === 'outofstock') {
                $query->where('stock', '<=', 0);
            } elseif ($filter === 'active') {
                $query->where('active', true);
            } elseif ($filter === 'inactive') {
                $query->where('active', false);
            }
        }

        $products = $query->paginate(15)->withQueryString();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.form', compact('categories'));
    }

    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        // Ensure images count does not exceed 3 (StoreProductRequest already limits to 3)
        $newImages = $request->file('images') ?? [];
        if (count($newImages) > 3) {
            return redirect()->back()->withErrors(['images' => 'Maximum 3 images allowed.'])->withInput();
        }

        try {
            DB::transaction(function () use ($validated, $request, $newImages) {
                $product = Product::create($validated);

                if (!empty($newImages)) {
                    $startPos = 0;
                    foreach ($newImages as $index => $image) {
                        if ($image && $image->isValid()) {
                            $path = $this->imageCompressionService->compressAndStore($image, targetSizeKb: 1024);
                            ProductImage::create([
                                'product_id' => $product->id,
                                'url' => $path,
                                'alt_text' => $product->name,
                                'position' => $startPos + $index,
                            ]);
                        }
                    }
                }
            });
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan produk atau gambar: ' . $e->getMessage());
            return redirect()->back()->withErrors(['images' => 'Gagal mengupload gambar. Pastikan file valid dan coba lagi.'])->withInput();
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.form', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $validated = $request->validated();

        $newImages = $request->file('images') ?? [];
        $existingCount = $product->images()->count();
        if (!empty($newImages) && ($existingCount + count($newImages) > 3)) {
            return redirect()->back()->withErrors(['images' => 'Total images (existing + new) cannot exceed 3.'])->withInput();
        }

        try {
            DB::transaction(function () use ($validated, $request, $product, $newImages) {
                $product->update($validated);

                if (!empty($newImages)) {
                    $maxPos = $product->images()->max('position');
                    $startPos = is_null($maxPos) ? 0 : $maxPos + 1;
                    foreach ($newImages as $index => $image) {
                        if ($image && $image->isValid()) {
                            $path = $this->imageCompressionService->compressAndStore($image, targetSizeKb: 1024);
                            ProductImage::create([
                                'product_id' => $product->id,
                                'url' => $path,
                                'alt_text' => $product->name,
                                'position' => $startPos + $index,
                            ]);
                        }
                    }
                }
            });
        } catch (\Exception $e) {
            Log::error('Gagal memperbarui produk atau upload gambar: ' . $e->getMessage());
            return redirect()->back()->withErrors(['images' => 'Gagal mengupload gambar. Pastikan file valid dan coba lagi.'])->withInput();
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        DB::transaction(function () use ($product) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->url);
                $image->delete();
            }
            $product->delete();
        });

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    /**
     * Delete a product image.
     */
    public function destroyImage(ProductImage $image)
    {
        $product = $image->product;
        DB::transaction(function () use ($image) {
            Storage::disk('public')->delete($image->url);
            $image->delete();
        });

        // Rebuild positions
        $product->refresh();
        $pos = 0;
        foreach ($product->images()->orderBy('position')->get() as $img) {
            $img->position = $pos++;
            $img->save();
        }

        return redirect()->back()->with('success', 'Image deleted.');
    }

    /**
     * Mark an image as primary (position 0) for its product.
     */
    public function setPrimaryImage(Product $product, ProductImage $image)
    {
        DB::transaction(function () use ($product, $image) {
            // Shift all images up by 1
            foreach ($product->images as $img) {
                if ($img->id === $image->id) continue;
                $img->position = $img->position + 1;
                $img->save();
            }

            // Set selected image position to 0
            $image->position = 0;
            $image->save();
        });

        return redirect()->back()->with('success', 'Primary image set.');
    }
}
