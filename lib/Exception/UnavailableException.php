<?php

declare(strict_types=1);
namespace Minichan\Exception;

use Minichan\Config\Status;

class UnavailableException extends GRPCException
{
    protected const CODE = Status::UNAVAILABLE;
}
