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

    /**
     * Client Settings
     */
    public const TIMEOUT = 10;
    public const OPEN_EOF_CHECK = true;
    public const PACKAGE_MAX_LENGTH = 2 * 1024 * 1024;
    public const HTTP2_MAX_CONCURRENT_STREAMS = 1000;
    public const HTT2_MAX_FRAME_SIZE = 2 * 1024 * 1024;
    public const MAX_RETRIES = 10;
}
