<?php

declare(strict_types=1);
namespace Minichan\Exception;

use Minichan\Config\Status;

class UnimplementedException extends GRPCException
{
    protected const CODE = Status::UNIMPLEMENTED;
}
