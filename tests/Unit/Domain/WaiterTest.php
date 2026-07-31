<?php

namespace Tests\Unit\Domain;

use App\Domain\Waiter\Entities\Waiter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class WaiterTest extends TestCase
{
    public function test_it_normalizes_its_data_and_changes_availability(): void
    {
        $waiter = new Waiter(
            id: null,
            name: '  Ana Gómez  ',
            employeeCode: '  M-01  ',
            phone: '  0981000000  ',
        );

        $this->assertSame('Ana Gómez', $waiter->name());
        $this->assertSame('M-01', $waiter->employeeCode());
        $this->assertSame('0981000000', $waiter->phone());
        $this->assertTrue($waiter->isActive());

        $waiter->deactivate();
        $this->assertFalse($waiter->isActive());

        $waiter->activate();
        $this->assertTrue($waiter->isActive());
    }

    public function test_it_converts_empty_optional_values_to_null(): void
    {
        $waiter = new Waiter(null, 'Ana', ' ', '');

        $this->assertNull($waiter->employeeCode());
        $this->assertNull($waiter->phone());
    }

    public function test_it_rejects_an_empty_name(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Waiter(null, '   ');
    }
}
