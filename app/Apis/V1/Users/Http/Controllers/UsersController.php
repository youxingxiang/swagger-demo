<?php
namespace App\Apis\V1\Users\Http\Controllers;

use App\Apis\V1\Base\Http\Controllers\ApiController;
use App\Apis\V1\Exceptions\ApiNotFoundException;
use App\Apis\V1\Users\Http\Requests\UsersRequest;
use App\Apis\V1\Users\Http\Resources\UserResource;
use App\Apis\V1\Users\Repositories\UserRepository;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UsersController extends ApiController
{
    /**
     * @OA\Get(
     *     tags={"Authorize"},
     *     path="/users",
     *     summary="get user list",
     *     security={{ "Bearer":{} }},
     *     @OA\Parameter(
     *         name="offset",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         ),
     *         in="query",
     *         description="offset",
     *         example=0,
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         ),
     *         in="query",
     *         description="offset",
     *         example=10,
     *         required=true,
     *     ),
     *     @OA\Response(response="401", description="fail", @OA\JsonContent(ref="#/components/schemas/ApiRequestException")),
     *     @OA\Response(response="200", description="success",@OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/UserResource"))))
     * )
     * @param  UsersRequest  $request
     * @param  UserRepository  $repository
     * @return AnonymousResourceCollection
     */
    public function index(
        UsersRequest $request,
        UserRepository $repository
    ):AnonymousResourceCollection {
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        return UserResource::collection($repository->get($offset, $limit));
    }

    /**
     * @OA\Get(
     *     tags={"Authorize"},
     *     path="/users/{id}",
     *     summary="get user detail",
     *     security={{ "Bearer":{} }},
     *     @OA\Parameter(
     *        name="id",
     *        in="path",
     *        description="user Id",
     *        @OA\Schema(
     *           type="integer",
     *           format="int64"
     *        ),
     *        required=true,
     *        example=1
     *     ),
     *     @OA\Response(response="401", description="fail", @OA\JsonContent(ref="#/components/schemas/ApiRequestException")),
     *     @OA\Response(response="404", description="fail", @OA\JsonContent(ref="#/components/schemas/ApiNotFoundException")),
     *     @OA\Response(response="200", description="success",@OA\JsonContent(ref="#/components/schemas/UserResource")))
     * )
     * @param  int  $id
     * @return UserResource
     */
    public function show(int $id)
    {
        try {
            return new UserResource(User::query()->findOrFail($id));
        } catch (\Exception $exception) {
            throw new ApiNotFoundException();
        }
    }
}
