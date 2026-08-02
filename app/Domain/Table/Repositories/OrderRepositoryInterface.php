<?php

namespace App\Domain\Table\Repositories;

use App\Domain\Table\Entities\Order;

interface OrderRepositoryInterface
{
    public function openForTableNumber(int $tableNumber): Order;

    public function findOpenByTableNumber(int $tableNumber): ?Order;

    public function addProduct(int $tableNumber, int $productId, bool $isAdmin, ?int $waiterId): Order;

    public function removeProductUnit(int $tableNumber, int $productId, bool $isAdmin, ?int $waiterId): Order;

    public function updateProductQuantity(int $tableNumber, int $productId, int $quantity, bool $isAdmin, ?int $waiterId): Order;

    public function removeProduct(int $tableNumber, int $productId, bool $isAdmin, ?int $waiterId): Order;

    public function closeByTableNumber(int $tableNumber, string $paymentMethod, bool $isAdmin, ?int $waiterId): Order;

    /** @return list<Order> */
    public function activeKitchenOrders(): array;

    public function updateKitchenStatus(int $orderId, string $nextStatus): Order;

    /** @return list<Order> */
    public function readyForWaiterSince(int $waiterId, string $since): array;

    public function openCount(): int;

    public function salesTodayInCents(): int;

    public function salesMonthInCents(): int;

    public function productsSoldToday(): int;

    /**
     * @return list<Order>
     */
    public function recentClosed(int $limit): array;

    /**
     * @return array<string, mixed>
     */
    public function dailyReport(string $date): array;

    /**
     * @return array<string, mixed>
     */
    public function monthlyReport(int $month, int $year): array;
}
