最近在新项目开发的过程中我发现 `swagger-php` 升级了版本，而且和以前的文档注释写法有了蛮多的差别。官方文档也写的不是很详细，在这里我将结合自己封装的案例将 `Swagger-PHP v3.x` 的一些用法分享给大家。  

- 升级后的区别 [Swagger-PHP v3.x Specification](https://github.com/zircote/swagger-php/blob/master/docs/Migrating-to-v3.md)

- [Swagger-PHP v3.x 官方文档](https://zircote.github.io/swagger-php/)

## 介绍

- [案例代码演示地址](https://github.com/youxingxiang/swagger-demo)

- API 开发目录

![Laravel 8 开发中使用 swagger-php 3 生成文档](https://cdn.learnku.com/uploads/images/202109/10/43464/Hfozw2uyZH.png!large)

- 文档生成后效果

![Laravel 8 开发中使用 swagger-php 3 生成文档](https://cdn.learnku.com/uploads/images/202109/10/43464/Mn4OhAxTdR.png!large)

## 安装
- `composer require darkaonline/l5-swagger`
- `php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"`
- `composer require laravel/sanctum`

## Swagger 可复用的公用参数
-  我会把文档的一些公共参数( 如 `@OA\OpenApi` , `@OA\OpenApi`,  `@OA\SecurityScheme`)写到 `ApiController` 里面。[代码](https://github.com/youxingxiang/swagger-demo/blob/main/app/Apis/V1/Base/Http/Controllers/ApiController.php)如下

```php
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

```

- 图片对应

![Laravel 8 开发中使用 swagger-php 3 生成文档](https://cdn.learnku.com/uploads/images/202109/10/43464/P70zhFdPWH.png!large)

## Swagger 请求参数
### POST 请求
在 `swagger-php 3.*` 以前我们如果 Post 请求参数很多，那么在控制器我们可能写很多的注释。导致代码特别冗余。 `swagger-php 3.*`  我是通过 [表单验证 ](https://learnku.com/docs/laravel/8.x/validation/9374) 来解决这个问题的。同时代码也更清晰规范。

- [用户注册案列分享](https://github.com/youxingxiang/swagger-demo/blob/main/app/Apis/V1/Users/Http/Controllers/RegisterController.php),代码如下

```php
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

```

在上面代码中，我们看不到任何需要请求的参数，`@OA\RequestBody` 通过  `ref ` 关联到 `#/components/schemas/RegisterRequest`,而我们刚好有个表单请求类 `RegisterRequest`。在  `swagger-php 3.* ` 我们可以把文档 post 参数的注释放到表单请求类 `RegisterRequest`。`RegisterRequest` 代码如下:
```php
<?php
namespace App\Apis\V1\Users\Http\Requests;

use App\Apis\V1\Base\Http\Requests\Request;

/**
 * @OA\Schema()
 */
class RegisterRequest extends Request
{

    /**
     * @OA\Property(format="string", default="xingxiang", description="name", property="name"),
     * @OA\Property(format="string", default="xingxiang@spacebib.com", description="email", property="email"),
     * @OA\Property(format="string", default="password", description="password", property="password"),
     * @OA\Property(format="string", default="password", description="password confirmation", property="password confirmation"),
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}

```
- 生成文档效果


![Laravel 8 开发中使用 swagger-php 3 生成文档](https://cdn.learnku.com/uploads/images/202109/10/43464/zavqW5YtdG.png!large)

### GET 请求
我们以获取用户列表和获取用户详情，分别演示路由参数和请求参数注释如何编写。
- [路由参数案例](https://github.com/youxingxiang/swagger-demo/blob/main/app/Apis/V1/Users/Http/Controllers/UsersController.php)(获取用户详情)

```php
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
```

![Laravel 8 开发中使用 swagger-php 3 生成文档](https://cdn.learnku.com/uploads/images/202109/10/43464/avX9apEZyL.png!large)


- [请求参数案例](https://github.com/youxingxiang/swagger-demo/blob/main/app/Apis/V1/Users/Http/Controllers/UsersController.php)(获取用户列表)

```php
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
```


![Laravel 8 开发中使用 swagger-php 3 生成文档](https://cdn.learnku.com/uploads/images/202109/10/43464/IvISgDFEgh.png!large)

## Swagger 返回参数
在 Api 返回的时候，有可能是列表，有可能是对象。如果对象参数特别多，按以前我们可能在控制器写很多注释文档，导致代码很难看，我在项目开发中是将返回的注释写到 [API 资源](https://learnku.com/docs/laravel/8.x/eloquent-resources/9410) 来解决这个问题。

### 返回数组列表
- [获取用户列表案例](https://github.com/youxingxiang/swagger-demo/blob/main/app/Apis/V1/Users/Http/Controllers/UsersController.php)

![Laravel 8 开发中使用 swagger-php 3 生成文档](https://cdn.learnku.com/uploads/images/202109/10/43464/QlfYore9tE.png!large)

[UserResource](https://github.com/youxingxiang/swagger-demo/blob/main/app/Apis/V1/Users/Http/Resources/UserResource.php) 代码

```php
<?php
namespace App\Apis\V1\Users\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Apis\V1\Users\Http\Resources
 * @OA\Schema(
 * )
 */
class UserResource extends JsonResource
{
    /**
     * @OA\Property(format="int64", title="ID", default=1, description="ID", property="id"),
     * @OA\Property(format="string", title="name", default="xingxiang", description="name", property="name"),
     * @OA\Property(format="string", title="email", default="xingxiang@test.com", description="email", property="email")
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            'name' => $this->name,
            'email' => $this->email,
        ];
    }
}

```

效果


![Laravel 8 开发中使用 swagger-php 3 生成文档](https://cdn.learnku.com/uploads/images/202109/10/43464/nsj9jcHLdQ.png!large)


### 返回对象
- [返回用户详情案例](https://github.com/youxingxiang/swagger-demo/blob/main/app/Apis/V1/Users/Http/Controllers/UsersController.php)


![Laravel 8 开发中使用 swagger-php 3 生成文档](https://cdn.learnku.com/uploads/images/202109/10/43464/XCDTeyBvml.png!large)


![Laravel 8 开发中使用 swagger-php 3 生成文档](https://cdn.learnku.com/uploads/images/202109/10/43464/DSN6Zeae96.png!large)


从上面我们可以发现 `UserResource` 是可以复用的，当 `@OA\JsonContent(ref="#/components/schemas/UserResource")` 里面有 `type = array` 返回的就是列表，不然就是对象。


### 异常返回

- [返回用户详情案例](https://github.com/youxingxiang/swagger-demo/blob/main/app/Apis/V1/Users/Http/Controllers/UsersController.php)


![Laravel 8 开发中使用 swagger-php 3 生成文档](https://cdn.learnku.com/uploads/images/202109/10/43464/Kz9MzxGg7m.png!large)


![Laravel 8 开发中使用 swagger-php 3 生成文档](https://cdn.learnku.com/uploads/images/202109/10/43464/1fQyfQucTI.png!large)

- ApiNotFoundException 代码

```php
<?php
namespace App\Apis\V1\Exceptions;

use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Schema()
 */
class ApiNotFoundException extends ApiException
{
    /**
     * The err message
     * @var string
     *
     * @OA\Property(
     *   property="message",
     *   type="string",
     *   example="Not Found"
     * )
     */
    public function __construct(string $message = null)
    {
        parent::__construct(self::NO_FOUND_ERROR, $message ?? Response::$statusTexts[self::NO_FOUND_ERROR]);
    }
}

```

## 总结
我是通过 [API 资源](https://learnku.com/docs/laravel/8.x/eloquent-resources/9410) 和 [表单验证](https://learnku.com/docs/laravel/8.x/validation/9374) 去拆解注释，同时达到 API 开发目录的规范。在我项目实际开发中，自己也基于这套规范收益良多。


