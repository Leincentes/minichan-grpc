<?php

declare(strict_types=1);
namespace Minichan\Exception;

use Minichan\Config\Status;

class DeadlineExceededException extends GrpcException
{
    protected const CODE = Status::DEADLINE_EXCEEDED;
}
