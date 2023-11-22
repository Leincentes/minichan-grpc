<?php

declare(strict_types=1);
namespace Minichan\Exception;

use Minichan\Config\Status;

class InvalidArgumentException extends GrpcException
{
    protected const CODE = Status::INVALID_ARGUMENT;
}