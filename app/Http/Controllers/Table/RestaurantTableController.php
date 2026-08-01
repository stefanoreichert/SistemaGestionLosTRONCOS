<?php

namespace App\Http\Controllers\Table;

use App\Application\Product\UseCases\ListActiveProductsUseCase;
use App\Application\Table\DTOs\AddProductToOrderDTO;
use App\Application\Table\DTOs\AuthenticatedOrderOperatorDTO;
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
use App\Domain\Table\Entities\Order;
use App\Domain\Table\Entities\RestaurantTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Table\AddProductToTableRequest;
use App\Http\Requests\Table\CloseTableOrderRequest;
use App\Http\Requests\Table\RemoveProductFromOrderRequest;
use App\Http\Requests\Table\SearchProductForTableRequest;
use App\Http\Requests\Table\UpdateProductQuantityRequest;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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
        Request $request,
        int $number,
        EnsureRestaurantTablesUseCase $ensureTables,
        OpenTableOrderUseCase $openOrder,
        ListActiveProductsUseCase $listProducts,
    ): View {
        $ensureTables->execute();
        $order = $openOrder->execute($number);
        $table = new RestaurantTable($order->tableId(), $number, $order);

        $products = $listProducts->execute();
        $user = $this->authenticatedUser($request);

        if (! Gate::forUser($user)->allows('view', $order)) {
            throw new AuthorizationException('No tiene permiso para acceder a este pedido.');
        }

        return view('tables.show', [
            'table' => $table,
            'products' => $products,
            'productsByCategory' => $this->groupProductsByCategory($products),
            'canAddProducts' => Gate::forUser($user)->allows('addProduct', $order),
            'canModifyOrder' => Gate::forUser($user)->allows('modify', $order),
            'isAssignedToAnotherWaiter' => $user->isWaiter()
                && $order->waiterId() !== null
                && $order->waiterId() !== (int) $user->waiter_id,
        ]);
    }

    public function addProduct(
        AddProductToTableRequest $request,
        int $number,
        GetRestaurantTableUseCase $getTable,
        AddProductToOrderUseCase $useCase,
    ): RedirectResponse {
        $this->authorizeAddProduct($request, $number, $getTable);

        $useCase->execute(new AddProductToOrderDTO(
            tableNumber: $number,
            productId: (int) $request->validated('product_id'),
        ), $this->authenticatedOperator($request));

        return redirect()->route('tables.show', $number)->with('status', 'Producto agregado a la mesa.');
    }

    public function addProductByName(
        SearchProductForTableRequest $request,
        int $number,
        GetRestaurantTableUseCase $getTable,
        ListActiveProductsUseCase $listProducts,
        AddProductToOrderUseCase $useCase,
    ): RedirectResponse {
        $this->authorizeAddProduct($request, $number, $getTable);
        $product = $this->findProductByName((string) $request->validated('product_name'), $listProducts->execute());

        if ($product === null) {
            return redirect()
                ->route('tables.show', $number)
                ->withErrors(['product_name' => 'No se encontro un producto con ese nombre.']);
        }

        $useCase->execute(new AddProductToOrderDTO(
            tableNumber: $number,
            productId: (int) $product->id(),
        ), $this->authenticatedOperator($request));

        return redirect()->route('tables.show', $number)->with('status', 'Producto agregado a la mesa.');
    }

    public function removeUnit(
        RemoveProductFromOrderRequest $request,
        int $number,
        GetRestaurantTableUseCase $getTable,
        RemoveProductUnitUseCase $useCase,
    ): RedirectResponse {
        $this->authorizeExistingOrder($request, $number, $getTable, 'modify');

        $useCase->execute(new RemoveProductFromOrderDTO(
            tableNumber: $number,
            productId: (int) $request->validated('product_id'),
        ), $this->authenticatedOperator($request));

        return redirect()->route('tables.show', $number)->with('status', 'Unidad eliminada.');
    }

    public function updateQuantity(
        UpdateProductQuantityRequest $request,
        int $number,
        GetRestaurantTableUseCase $getTable,
        UpdateProductQuantityUseCase $useCase,
    ): RedirectResponse {
        $this->authorizeExistingOrder($request, $number, $getTable, 'modify');

        $useCase->execute(new UpdateProductQuantityDTO(
            tableNumber: $number,
            productId: (int) $request->validated('product_id'),
            quantity: (int) $request->validated('quantity'),
        ), $this->authenticatedOperator($request));

        return redirect()->route('tables.show', $number)->with('status', 'Cantidad actualizada.');
    }

    public function removeProduct(
        RemoveProductFromOrderRequest $request,
        int $number,
        GetRestaurantTableUseCase $getTable,
        RemoveProductFromOrderUseCase $useCase,
    ): RedirectResponse {
        $this->authorizeExistingOrder(
            $request,
            $number,
            $getTable,
            'modify',
            'No puede quitar productos porque este pedido pertenece a otro mozo.',
        );

        $useCase->execute(new RemoveProductFromOrderDTO(
            tableNumber: $number,
            productId: (int) $request->validated('product_id'),
        ), $this->authenticatedOperator($request));

        return redirect()->route('tables.show', $number)->with('success', 'Producto quitado del pedido.');
    }

    public function close(
        CloseTableOrderRequest $request,
        int $number,
        GetRestaurantTableUseCase $getTable,
        CloseTableOrderUseCase $useCase,
    ): View {
        $this->authorizeExistingOrder($request, $number, $getTable, 'close');

        return view('tables.ticket', [
            'order' => $useCase->execute(new CloseTableOrderDTO(
                tableNumber: $number,
                paymentMethod: (string) $request->validated('payment_method'),
            ), $this->authenticatedOperator($request)),
        ]);
    }

    /**
     * @param  array<int, object>  $products
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
     * @param  array<int, object>  $products
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

    private function authorizeAddProduct(
        Request $request,
        int $tableNumber,
        GetRestaurantTableUseCase $getTable,
    ): void {
        $order = $getTable->execute($tableNumber)?->openOrder();
        $user = $this->authenticatedUser($request);
        $allowed = $order instanceof Order
            ? Gate::forUser($user)->allows('addProduct', $order)
            : Gate::forUser($user)->allows('createWithProduct', Order::class);

        if (! $allowed) {
            throw new AuthorizationException('No tiene permiso para modificar este pedido.');
        }
    }

    private function authorizeExistingOrder(
        Request $request,
        int $tableNumber,
        GetRestaurantTableUseCase $getTable,
        string $ability,
        string $message = 'No tiene permiso para modificar este pedido.',
    ): void {
        $order = $getTable->execute($tableNumber)?->openOrder();

        if (! $order instanceof Order
            || ! Gate::forUser($this->authenticatedUser($request))->allows($ability, $order)) {
            throw new AuthorizationException($message);
        }
    }

    private function authenticatedOperator(Request $request): AuthenticatedOrderOperatorDTO
    {
        $user = $this->authenticatedUser($request);

        return new AuthenticatedOrderOperatorDTO(
            isAdmin: $user->isAdmin(),
            waiterId: $user->isWaiter() && $user->waiter_id !== null ? (int) $user->waiter_id : null,
        );
    }

    private function authenticatedUser(Request $request): User
    {
        $user = $request->user();

        if (! $user instanceof User) {
            throw new AuthorizationException('No tiene permiso para modificar este pedido.');
        }

        return $user;
    }
}
