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
    public const UNKNOWN = 2;            // Unknown error occurred.
    public const INVALID_ARGUMENT = 3;   // Client specified an invalid argument.
    public const DEADLINE_EXCEEDED = 4;  // Deadline expired before operation could complete.
    public const NOT_FOUND = 5;          // Specified entity not found.
    public const ALREADY_EXISTS = 6;     // Entity that the operation is attempting to create already exists.
    public const PERMISSION_DENIED = 7;  // Caller does not have permission to execute the specified operation.
    public const RESOURCE_EXHAUSTED = 8; // Resource has been exhausted (e.g., out of memory, disk space).
    public const FAILED_PRECONDITION = 9; // Operation was rejected because the system is not in a state required for the operation.
    public const ABORTED = 10;            // The operation was aborted (usually due to a concurrency issue like sequencer check failures, transaction aborts, etc.).
    public const OUT_OF_RANGE = 11;       // Operation was attempted past the valid range.
    public const UNIMPLEMENTED = 12;      // Operation is not implemented or not supported/enabled.
    public const INTERNAL = 13;           // Internal errors.
    public const UNAVAILABLE = 14;        // Service is currently unavailable.
    public const DATA_LOSS = 15;          // Unrecoverable data loss or corruption.
    public const UNAUTHENTICATED = 16;   // Request does not have valid authentication credentials.
}