<?php

namespace App\Http\Controllers\Table;

use App\Application\Product\UseCases\ListActiveProductsUseCase;
use App\Application\Table\DTOs\AddProductToOrderDTO;
use App\Application\Table\DTOs\CloseTableOrderDTO;
use App\Application\Table\DTOs\RemoveProductFromOrderDTO;
use App\Application\Table\DTOs\UpdateProductQuantityDTO;
use App\Application\Table\UseCases\AddProductToOrderUseCase;
use App\Application\Table\UseCases\CloseTableOrderUseCase;
use App\Application\Table\UseCases\EnsureRestaurantTablesUseCase;
use App\Application\Table\UseCases\GetRestaurantTableUseCase;
use App\Application\Table\UseCases\ListRestaurantTablesUseCase;
use App\Application\Table\UseCases\OpenTableOrderUseCase;
use App\Application\Table\UseCases\RemoveProductFromOrderUseCase;
use App\Application\Table\UseCases\RemoveProductUnitUseCase;
use App\Application\Table\UseCases\UpdateProductQuantityUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Table\AddProductToTableRequest;
use App\Http\Requests\Table\CloseTableOrderRequest;
use App\Http\Requests\Table\RemoveProductFromOrderRequest;
use App\Http\Requests\Table\SearchProductForTableRequest;
use App\Http\Requests\Table\UpdateProductQuantityRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RestaurantTableController extends Controller
{
    public function index(
        EnsureRestaurantTablesUseCase $ensureTables,
        ListRestaurantTablesUseCase $listTables,
    ): View {
        $ensureTables->execute();

        return view('tables.index', [
            'tables' => $listTables->execute(),
        ]);
    }

    public function show(
        int $number,
        EnsureRestaurantTablesUseCase $ensureTables,
        OpenTableOrderUseCase $openOrder,
        GetRestaurantTableUseCase $getTable,
        ListActiveProductsUseCase $listProducts,
    ): View {
        $ensureTables->execute();
        $openOrder->execute($number);
        $table = $getTable->execute($number);

        abort_if($table === null, 404);

        $products = $listProducts->execute();

        return view('tables.show', [
            'table' => $table,
            'products' => $products,
            'productsByCategory' => $this->groupProductsByCategory($products),
        ]);
    }

    public function addProduct(
        AddProductToTableRequest $request,
        int $number,
        AddProductToOrderUseCase $useCase,
    ): RedirectResponse {
        $useCase->execute(new AddProductToOrderDTO(
            tableNumber: $number,
            productId: (int) $request->validated('product_id'),
        ));

        return redirect()->route('tables.show', $number)->with('status', 'Producto agregado a la mesa.');
    }

    public function addProductByName(
        SearchProductForTableRequest $request,
        int $number,
        ListActiveProductsUseCase $listProducts,
        AddProductToOrderUseCase $useCase,
    ): RedirectResponse {
        $product = $this->findProductByName((string) $request->validated('product_name'), $listProducts->execute());

        if ($product === null) {
            return redirect()
                ->route('tables.show', $number)
                ->withErrors(['product_name' => 'No se encontro un producto con ese nombre.']);
        }

        $useCase->execute(new AddProductToOrderDTO(
            tableNumber: $number,
            productId: (int) $product->id(),
        ));

        return redirect()->route('tables.show', $number)->with('status', 'Producto agregado a la mesa.');
    }

    public function removeUnit(
        RemoveProductFromOrderRequest $request,
        int $number,
        RemoveProductUnitUseCase $useCase,
    ): RedirectResponse {
        $useCase->execute(new RemoveProductFromOrderDTO(
            tableNumber: $number,
            productId: (int) $request->validated('product_id'),
        ));

        return redirect()->route('tables.show', $number)->with('status', 'Unidad eliminada.');
    }

    public function updateQuantity(
        UpdateProductQuantityRequest $request,
        int $number,
        UpdateProductQuantityUseCase $useCase,
    ): RedirectResponse {
        $useCase->execute(new UpdateProductQuantityDTO(
            tableNumber: $number,
            productId: (int) $request->validated('product_id'),
            quantity: (int) $request->validated('quantity'),
        ));

        return redirect()->route('tables.show', $number)->with('status', 'Cantidad actualizada.');
    }

    public function removeProduct(
        RemoveProductFromOrderRequest $request,
        int $number,
        RemoveProductFromOrderUseCase $useCase,
    ): RedirectResponse {
        $useCase->execute(new RemoveProductFromOrderDTO(
            tableNumber: $number,
            productId: (int) $request->validated('product_id'),
        ));

        return redirect()->route('tables.show', $number)->with('status', 'Producto eliminado del pedido.');
    }

    public function close(CloseTableOrderRequest $request, int $number, CloseTableOrderUseCase $useCase): View
    {
        return view('tables.ticket', [
            'order' => $useCase->execute(new CloseTableOrderDTO(
                tableNumber: $number,
                paymentMethod: (string) $request->validated('payment_method'),
            )),
        ]);
    }

    /**
     * @param array<int, object> $products
     * @return array<string, array<int, object>>
     */
    private function groupProductsByCategory(array $products): array
    {
        $grouped = [];

        foreach ($products as $product) {
            $grouped[$product->category()][] = $product;
        }

        ksort($grouped);

        return $grouped;
    }

    /**
     * @param array<int, object> $products
     */
    private function findProductByName(string $search, array $products): ?object
    {
        $normalizedSearch = mb_strtolower(trim($search));
        $partialMatch = null;

        foreach ($products as $product) {
            $name = mb_strtolower($product->name());

            if ($name === $normalizedSearch) {
                return $product;
            }

            if ($partialMatch === null && str_contains($name, $normalizedSearch)) {
                $partialMatch = $product;
            }
        }

        return $partialMatch;
    }
}
