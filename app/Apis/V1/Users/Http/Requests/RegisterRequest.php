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
