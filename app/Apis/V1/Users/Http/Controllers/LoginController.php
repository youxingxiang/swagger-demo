<?php
namespace App\Apis\V1\Users\Http\Controllers;

use App\Apis\V1\Base\Http\Controllers\ApiController;
use App\Apis\V1\Exceptions\ApiRequestException;
use App\Apis\V1\Users\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends ApiController
{
    /**
     * @OA\Post  (
     *     tags={"UnAuthorize"},
     *     path="/user/login",
     *     summary="user login",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 ref="#/components/schemas/LoginRequest",
     *             )
     *         )
     *     ),
     *     @OA\Response(response="401", description="fail", @OA\JsonContent(ref="#/components/schemas/ApiRequestException")),
     *     @OA\Response(response="200", description="An example resource", @OA\JsonContent(type="object", @OA\Property(format="string", default="20d338931e8d6bd9466edeba78ea7dce7c7bc01aa5cc5b4735691c50a2fe3228", description="token", property="token"))),
     * )
     */
    public function login(LoginRequest $request)
    {
        $user = User::query()->where('email', $request->email)->firstOrFail();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw new ApiRequestException('The provided credentials are incorrect.');
        }
        return response(["token" => $user->createToken($user->email)->plainTextToken]);
    }
}
