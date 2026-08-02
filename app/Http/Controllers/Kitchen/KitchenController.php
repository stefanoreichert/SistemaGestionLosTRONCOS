<?php

namespace App\Http\Controllers\Kitchen;

use App\Application\Table\UseCases\ListKitchenOrdersUseCase;
use App\Application\Table\UseCases\UpdateKitchenStatusUseCase;
use App\Domain\Table\Entities\Order;
use App\Http\Controllers\Controller;
use App\Http\Requests\Kitchen\UpdateKitchenStatusRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class KitchenController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeKitchen($request, 'viewKitchen');

        return view('kitchen.index');
    }

    public function orders(Request $request, ListKitchenOrdersUseCase $useCase): JsonResponse
    {
        $this->authorizeKitchen($request, 'viewKitchen');

        return response()->json([
            'server_now' => now()->toIso8601String(),
            'orders' => array_map($this->serialize(...), $useCase->execute()),
        ]);
    }

    public function update(
        UpdateKitchenStatusRequest $request,
        int $order,
        UpdateKitchenStatusUseCase $useCase,
    ): JsonResponse {
        $this->authorizeKitchen($request, 'transitionKitchen');

        try {
            $updated = $useCase->execute($order, (string) $request->validated('status'));
        } catch (\DomainException $exception) {
            throw ValidationException::withMessages(['status' => $exception->getMessage()]);
        }

        return response()->json(['order' => $this->serialize($updated)]);
    }

    private function authorizeKitchen(Request $request, string $ability): void
    {
        if (! Gate::forUser($request->user())->allows($ability, Order::class)) {
            throw new AuthorizationException('No tiene permiso para operar el panel de cocina.');
        }
    }

    private function serialize(Order $order): array
    {
        return [
            'id' => $order->id(),
            'table_number' => $order->tableNumber(),
            'waiter_name' => $order->waiterName() ?? 'Mozo no asignado',
            'status' => $order->kitchenStatus(),
            'sent_to_kitchen_at' => $order->sentToKitchenAt(),
            'items' => array_values(array_map(
                static fn ($item): array => ['name' => $item->productName(), 'quantity' => $item->quantity()],
                array_filter($order->items(), static fn ($item): bool => $item->requiresKitchen()),
            )),
        ];
    }
}
