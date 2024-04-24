<?php

namespace App\Exceptions;

use Exception;

class ErrorMessageException extends Exception
{
    protected $message;

    protected $code;

    public function __construct($message, $code = 400, ?Exception $previous = null)
    {
        $this->message = $message;
        $this->code = $code;
        parent::__construct($message, $code, $previous);
    }

    public function render($request)
    {
        $errorMessage = $this->message ?? $this->getMessage();

        return response()->json([
            'error' => $errorMessage,
            'errors' => null,
            'message' => $errorMessage,
        ], $this->code);
    }
}
