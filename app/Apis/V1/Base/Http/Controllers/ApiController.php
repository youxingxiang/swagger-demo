<?php
namespace App\Apis\V1\Base\Http\Controllers;

use App\Http\Controllers\Controller;

abstract class ApiController extends Controller
{
    /**
     * @OA\OpenApi(
     *   @OA\Server(
     *      url="/api/v1"
     *   ),
     *   @OA\Info(
     *      title="Swagger-Demo",
     *      version="1.0.0",
     *   ),
     * )
     */



    /**
     *@OA\Tag(name="UnAuthorize", description="No user login required")
     */

    /**
     *@OA\Tag(name="Authorize", description="User login required")
     */


    /**
     * @OA\SecurityScheme(
     *       scheme="Bearer",
     *       securityScheme="Bearer",
     *       type="apiKey",
     *       in="header",
     *       name="Authorization",
     * )
     */
}
