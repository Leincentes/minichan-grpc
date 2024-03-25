<?php
declare(strict_types=1);

namespace Minichan\Exception;

use Exception;

class TransientFailureException extends Exception
{
    public function __construct($message = 'Transient failure', $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
