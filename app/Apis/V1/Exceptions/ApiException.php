<?php
namespace App\Apis\V1\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;

class ApiException extends HttpException
{
    const DB_ERROR = FoundationResponse::HTTP_NOT_IMPLEMENTED;
    const AUTH_ERROR = FoundationResponse::HTTP_UNAUTHORIZED;
    const PERMISSION_ERROR = FoundationResponse::HTTP_FORBIDDEN;
    const NO_FOUND_ERROR = FoundationResponse::HTTP_NOT_FOUND;
    const SYS_ERROR = FoundationResponse::HTTP_INTERNAL_SERVER_ERROR;
    const BAD_REQUEST = FoundationResponse::HTTP_BAD_REQUEST;

    /**
     * ApiException constructor.
     * @param  int  $statusCode
     * @param  string|null  $message
     */
    public function __construct(int $statusCode, string $message = null)
    {
        parent::__construct($statusCode, $message);
    }
}
