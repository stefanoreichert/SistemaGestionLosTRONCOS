<?php

namespace App\Http\Controllers\Product;

use App\Application\Product\DTOs\ProductInputDTO;
use App\Application\Product\UseCases\CreateProductUseCase;
use App\Application\Product\UseCases\DeleteProductUseCase;
use App\Application\Product\UseCases\GetProductUseCase;
use App\Application\Product\UseCases\ListProductsUseCase;
use App\Application\Product\UseCases\UpdateProductUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(ListProductsUseCase $useCase): View
    {
        return view('products.index', [
            'products' => $useCase->execute(),
        ]);
    }

    public function create(): View
    {
        return view('products.create');
    }

    public function store(StoreProductRequest $request, CreateProductUseCase $useCase): RedirectResponse
    {
        $useCase->execute($this->toDto($request->validated()));

        return redirect()->route('products.index')->with('status', 'Producto creado correctamente.');
    }

    public function edit(int $product, GetProductUseCase $useCase): View
    {
        $entity = $useCase->execute($product);

        abort_if($entity === null, 404);

        return view('products.edit', [
            'product' => $entity,
        ]);
    }

    public function update(
        UpdateProductRequest $request,
        int $product,
        UpdateProductUseCase $useCase,
    ): RedirectResponse {
        $useCase->execute($product, $this->toDto($request->validated()));

        return redirect()->route('products.index')->with('status', 'Producto actualizado correctamente.');
    }

    public function destroy(int $product, DeleteProductUseCase $useCase): RedirectResponse
    {
        $useCase->execute($product);

        return redirect()->route('products.index')->with('status', 'Producto eliminado correctamente.');
    }

    /**
     * @param array<string, mixed> $data
     */
    private function toDto(array $data): ProductInputDTO
    {
        return new ProductInputDTO(
            name: (string) $data['name'],
            priceInCents: (int) round(((float) $data['price']) * 100),
            category: (string) $data['category'],
        );
    }
}
