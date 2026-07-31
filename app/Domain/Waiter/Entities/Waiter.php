<?php

namespace App\Domain\Waiter\Entities;

final class Waiter
{
    public function __construct(
        private readonly ?int $id,
        private string $name,
        private ?string $employeeCode = null,
        private ?string $phone = null,
        private bool $isActive = true,
    ) {
        $this->rename($name);
        $this->changeEmployeeCode($employeeCode);
        $this->changePhone($phone);
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function employeeCode(): ?string
    {
        return $this->employeeCode;
    }

    public function phone(): ?string
    {
        return $this->phone;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function activate(): void
    {
        $this->isActive = true;
    }

    public function deactivate(): void
    {
        $this->isActive = false;
    }

    public function rename(string $name): void
    {
        $name = trim($name);

        if ($name === '') {
            throw new \InvalidArgumentException('El nombre del mozo es obligatorio.');
        }

        $this->name = $name;
    }

    public function changeEmployeeCode(?string $employeeCode): void
    {
        $this->employeeCode = $this->normalizeOptionalValue($employeeCode);
    }

    public function changePhone(?string $phone): void
    {
        $this->phone = $this->normalizeOptionalValue($phone);
    }

    private function normalizeOptionalValue(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
