<?php

namespace App\Exceptions;

use Exception;

class AbsenAccessDeniedException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct('Only cadets can access attendance app');
    }
}
