<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = $request->query('status');

        $categories = Category::query()
            ->withCount('products')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('categories.index', [
            'categories' => $categories,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('categories.create', [
            'category' => new Category(['is_active' => true]),
        ]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $data = $this->validatedData($request->validated());

        Category::create($data);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category): View
    {
        return view('categories.edit', [
            'category' => $category,
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $this->validatedData($request->validated());

        $category->update($data);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            $category->update(['is_active' => false]);

            return redirect()
                ->route('categories.index')
                ->with('success', 'Kategori memiliki produk, jadi kategori dinonaktifkan agar data tetap aman.');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }

    public function toggle(Category $category): RedirectResponse
    {
        $category->update([
            'is_active' => ! $category->is_active,
        ]);

        return redirect()->route('categories.index')->with('success', 'Status kategori berhasil diperbarui.');
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function validatedData(array $data): array
    {
        $name = trim((string) $data['name']);
        $slug = trim((string) ($data['slug'] ?? ''));

        return [
            'name' => $name,
            'slug' => $slug !== '' ? Str::slug($slug) : Str::slug($name),
            'description' => $data['description'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ];
    }
}
