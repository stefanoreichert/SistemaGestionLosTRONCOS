<?php

namespace App\Domain\Reports\Exceptions;

use DomainException;

final class EmptyDailyReportPeriod extends DomainException
{
    public function __construct()
    {
        parent::__construct('No hay operaciones nuevas para cerrar.');
    }
}
