<?php

namespace App\Infrastructure\Providers;

use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Reports\Repositories\DailySalesReportRepositoryInterface;
use App\Domain\Reports\Repositories\SoldProductReportRepositoryInterface;
use App\Domain\Table\Repositories\OrderRepositoryInterface;
use App\Domain\Table\Repositories\RestaurantTableRepositoryInterface;
use App\Application\Table\Ports\TicketPrinterInterface;
use App\Domain\Tickets\Repositories\TicketRepositoryInterface;
use App\Infrastructure\Persistence\Repositories\EloquentDailySalesReportRepository;
use App\Infrastructure\Persistence\Repositories\EloquentOrderRepository;
use App\Infrastructure\Persistence\Repositories\EloquentProductRepository;
use App\Infrastructure\Persistence\Repositories\EloquentRestaurantTableRepository;
use App\Infrastructure\Persistence\Repositories\EloquentSoldProductReportRepository;
use App\Infrastructure\Persistence\Repositories\EloquentTicketRepository;
use App\Infrastructure\Printing\TicketPrinterService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        ProductRepositoryInterface::class => EloquentProductRepository::class,
        DailySalesReportRepositoryInterface::class => EloquentDailySalesReportRepository::class,
        SoldProductReportRepositoryInterface::class => EloquentSoldProductReportRepository::class,
        RestaurantTableRepositoryInterface::class => EloquentRestaurantTableRepository::class,
        OrderRepositoryInterface::class => EloquentOrderRepository::class,
        TicketRepositoryInterface::class => EloquentTicketRepository::class,
        TicketPrinterInterface::class => TicketPrinterService::class,
    ];
}
