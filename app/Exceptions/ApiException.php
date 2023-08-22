<?php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    protected $code = 500;
}
