<?php

declare(strict_types=1);
namespace Minichan\Exception;

use Minichan\Config\Status;

class NotFoundException extends GRPCException
{
    protected const CODE = Status::NOT_FOUND;
}
