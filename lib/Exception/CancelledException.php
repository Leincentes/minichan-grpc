<?php

declare(strict_types=1);
namespace Minichan\Exception;

use Minichan\Config\Status;

class CancelledException extends GrpcException
{
    protected const CODE = Status::CANCELLED;
}
