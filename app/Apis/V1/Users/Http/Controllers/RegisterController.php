<?php

namespace App\Apis\V1\Users\Http\Controllers;

use App\Apis\V1\Base\Http\Controllers\ApiController;
use App\Apis\V1\Users\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

class RegisterController extends ApiController
{
    /**
     * @OA\Post  (
     *     tags={"UnAuthorize"},
     *     path="/user/register",
     *     summary="user register",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 ref="#/components/schemas/RegisterRequest",
     *             )
     *         )
     *     ),
     *     @OA\Response(response="401", description="fail", @OA\JsonContent(ref="#/components/schemas/ApiRequestException")),
     *     @OA\Response(response="200", description="An example resource", @OA\JsonContent(type="object", @OA\Property(format="string", default="20d338931e8d6bd9466edeba78ea7dce7c7bc01aa5cc5b4735691c50a2fe3228", description="token", property="token"))),
     * )
     */
    public function register(RegisterRequest $request)
    {
        $params = $request->validated();

        $user = User::query()->create([
            'name' => $params['name'],
            'email' => $params['email'],
            'password' => Hash::make($params['password']),
        ]);
        event(new Registered($user));

        return response(["token" => $user->createToken($params['email'])->plainTextToken]);
    }
}
