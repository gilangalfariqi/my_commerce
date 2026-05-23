<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\SEO\MetaService;
use App\Services\SEO\SchemaService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected MetaService $metaService;
    protected SchemaService $schemaService;

    public function __construct(MetaService $metaService, SchemaService $schemaService)
    {
        $this->metaService = $metaService;
        $this->schemaService = $schemaService;
    }

    public function index(Request $request): View
    {
        $query = Product::active()->with(['primaryImage', 'categories']);

        // Search filter
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Category filter
        if ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->whereHas('categories', function ($q) use ($category) {
                    $q->where('categories.id', $category->id);
                });
            }
        }

        // Sort filter
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'latest':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::all();

        // SEO meta
        $this->metaService->setDefault();

        return view('frontend.products.index', compact('products', 'categories'));
    }

    public function show(string $slug): View
    {
        $product = Product::active()
            ->where('slug', $slug)
            ->with(['categories', 'variants', 'images', 'primaryImage'])
            ->firstOrFail();

        $categoryIds = $product->categories->pluck('id')->toArray();
        $relatedProducts = Product::active()
            ->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('categories.id', $categoryIds);
            })
            ->where('id', '!=', $product->id)
            ->with('primaryImage')
            ->take(4)
            ->get();

        // SEO Meta
        $this->metaService->setForProduct($product);
        $productSchema = $this->schemaService->productSchema($product);

        $firstCategory = $product->categories->first();
        $breadcrumb = $this->schemaService->breadcrumbSchema([
            ['name' => 'Home', 'url' => url('/')],
            ['name' => $firstCategory?->name ?? 'Products', 'url' => route('products.index', ['category' => $firstCategory?->slug])],
            ['name' => $product->name, 'url' => route('products.show', $product->slug)]
        ]);

        return view('frontend.products.show', compact('product', 'relatedProducts', 'productSchema', 'breadcrumb'));
    }

    public function searchAutocomplete(Request $request): JsonResponse
    {
        $search = $request->get('q');
        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $products = Product::active()
            ->where('name', 'like', '%' . $search . '%')
            ->with('primaryImage')
            ->take(5)
            ->get()
            ->map(fn($product) => [
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => number_format($product->price, 0, ',', '.'),
                'image' => $product->primaryImage?->url ?? 'https://via.placeholder.com/150',
                'url' => route('products.show', $product->slug),
            ]);

        return response()->json($products);
    }
}
