<?php

declare(strict_types=1);
namespace Minichan\Grpc;

use Minichan\Config\Status;

/**
 * 
 *
 * Utility functions for various tasks.
 */
class Util
{
    /**
     * Get the version information.
     *
     * @return string
     */
    public static function getVersion(): string
    {
        return '1.0.0';
    }

    /**
     * Get the error message for a given error code and type.
     *
     * @param int $errorCode
     * @param int|null $errorType
     *
     * @return string
     */
    public static function getErrorMessage(int $errorCode, ?int $errorType): string
    {
        return 'Error Message';
    }
    
    /**
     * Log a message at the specified level.
     *
     * @param int $level
     * @param string $message
     */
    public static function log(int $level, string $message): void
    {
        switch ($level) {
            case Status::OK:
                $color = "\033[32m"; // Green
                break;
            case Status::CANCELLED:
                $color = "\033[33m"; // Yellow
                break;
            case Status::UNKNOWN:
            case Status::INVALID_ARGUMENT:
            case Status::DEADLINE_EXCEEDED:
            case Status::NOT_FOUND:
            case Status::ALREADY_EXISTS:
            case Status::UNIMPLEMENTED:
            case Status::INTERNAL:
            case Status::UNAVAILABLE:
                $color = "\033[31m"; // Red
                break;
            case Status::LOG:
                $color = "\033[32m";
                break;
            default:
                $color = "\033[0m"; // Reset color
                break;
        }
        echo "{$color}{$message}\033[0m\n";
    }

    /**
     * Calculate the hash code for a given content and type.
     *
     * @param string $content
     * @param int $type
     *
     * @return int|bool
     */
    public static function hashcode(string $content, int $type)
    {
        return crc32($content);
    }

    /**
     * Add a MIME type association for a given suffix.
     *
     * @param string $suffix
     * @param string $mimeType
     *
     * @return bool
     */
    public static function mimeTypeAdd(string $suffix, string $mimeType): bool
    {
        return true;
    }

    /**
     * Set the MIME type association for a given suffix.
     *
     * @param string $suffix
     * @param string $mimeType
     *
     * @return bool
     */
    public static function mimeTypeSet(string $suffix, string $mimeType): bool
    {
        return true;
    }

    /**
     * Delete the MIME type association for a given suffix.
     *
     * @param string $suffix
     *
     * @return bool
     */
    public static function mimeTypeDel(string $suffix): bool
    {
        return true;
    }

    /**
     * Get the MIME type for a given filename.
     *
     * @param string $filename
     *
     * @return string
     */
    public static function mimeTypeGet(string $filename): string
    {
        return 'application/octet-stream';
    }

    /**
     * List all MIME types.
     *
     * @return array
     */
    public static function mimeTypeList(): array
    {
        return ['txt' => 'text/plain', 'jpg' => 'image/jpeg'];
    }

    /**
     * Check if a MIME type exists for a given filename.
     *
     * @param string $filename
     *
     * @return string
     */
    public static function mimeTypeExists(string $filename): string
    {
        return 'application/octet-stream';
    }

    /**
     * Set the process name.
     *
     * @param string $name
     */
    public static function setProcessName(string $name): void
    {
        cli_set_process_title($name);
    }

    /**
     * Set asynchronous I/O (AIO) settings.
     *
     * @param array $settings
     *
     * @return bool
     */
    public static function setAio(array $settings): bool
    {
        return true;
    }
    
    /**
     * @param int $milliseconds [required]
     */
    public static function usleep(int $milliseconds): bool
    {
        return true;
    }
}
