<?php

declare(strict_types=1);
namespace Minichan\Config;

/**
 *
 * Represents GRPC status codes as constants.
 * These codes are used to indicate the status of a GRPC operation.
 */
final class Status
{
    public const OK = 0;                 // The operation completed successfully.
    public const CANCELLED = 1;          // The operation was cancelled (typically by the caller).
    public const LOG = 2;               // Logs of server.
    public const INVALID_ARGUMENT = 3;   // Client specified an invalid argument.
    public const DEADLINE_EXCEEDED = 4;  // Deadline expired before operation could complete.
    public const NOT_FOUND = 5;          // Specified entity not found.
    public const ALREADY_EXISTS = 6;     // Entity that the operation is attempting to create already exists.
    public const UNIMPLEMENTED = 7;      // Operation is not implemented or not supported/enabled.
    public const INTERNAL = 8;           // Internal errors.
    public const UNAVAILABLE = 9;        // Is currently unavailable.
    public const UNKNOWN = 10;          // Unknown error occurred.
}