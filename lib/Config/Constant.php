<?php
declare(strict_types=1);

namespace Minichan\Config;

/**
 * 
 * Contains constant values used in functionality.
 */
final class Constant
{
    /**
     * HTTP header key for content type.
     */
    public const CONTENT_TYPE = 'content-type';

    /**
     * gRPC status HTTP header key.
     */
    public const GRPC_STATUS = 'grpc-status';

    /**
     * gRPC message HTTP header key.
     */
    public const GRPC_MESSAGE = 'grpc-message';

    /**
     * Represents a gRPC call type.
     */
    public const GRPC_CALL = 1;

    /**
     * Represents a gRPC stream type.
     */
    public const GRPC_STREAM = 2;

    /**
     * Option to enable coroutine support.
     */
    public const OPTION_ENABLE_COROUTINE = true;

    public const SERVER_HOST = '127.0.0.1';

    public const SERVER_PORT = 9502;

    public const DB_HOST = 'localhost';
    public const DB_USERNAME = 'root';
    public const DB_PASSWORD = '';
    public const DB_DATABASE = '';
}