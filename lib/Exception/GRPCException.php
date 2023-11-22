<?php
declare(strict_types=1);
namespace Minichan\Exception;

use Minichan\Config\Status;
use RuntimeException;
use Throwable;

/**
 * 
 *
 * A base exception class for GRPC-related exceptions. It extends RuntimeException and provides a standardized way to create GRPC exceptions with a specified message, code, and previous exception.
 */
class GRPCException extends RuntimeException
{
    /**
     * Default GRPC exception code.
     */
    protected const CODE = Status::UNKNOWN;

    /**
     * GRPCException constructor.
     *
     * @param string $message
     * @param int|null $code
     * @param Throwable|null $previous
     */
    final public function __construct(
        string $message = '',
        int $code = null,
        Throwable $previous = null
    ) {
        parent::__construct($message, (int) ($code ?? static::CODE), $previous);
    }

    /**
     * Create a new instance of GRPCException.
     *
     * @param string $message
     * @param int|null $code
     * @param Throwable|null $previous
     *
     * @return static
     */
    public static function create(
        string $message,
        int $code = null,
        Throwable $previous = null
    ): self {
        return new static($message, $code, $previous);
    }
}
