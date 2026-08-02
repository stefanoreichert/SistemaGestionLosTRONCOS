<?php

namespace App\Http\Controllers\Kitchen;

use App\Application\Table\UseCases\ListReadyKitchenOrdersForWaiterUseCase;
use App\Domain\Table\Entities\Order;
use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class WaiterKitchenNotificationController extends Controller
{
    public function __invoke(Request $request, ListReadyKitchenOrdersForWaiterUseCase $useCase): JsonResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User && $user->isWaiter() && $user->waiter_id !== null, 403);
        $since = (string) $request->query('since', now()->toIso8601String());

        return response()->json([
            'server_now' => now()->toIso8601String(),
            'orders' => array_map(static fn (Order $order): array => [
                'id' => $order->id(),
                'table_number' => $order->tableNumber(),
                'ready_at' => $order->kitchenReadyAt(),
            ], $useCase->execute((int) $user->waiter_id, $since)),
        ]);
    }
}
