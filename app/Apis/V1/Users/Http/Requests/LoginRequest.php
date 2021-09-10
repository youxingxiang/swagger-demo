<?php
namespace App\Apis\V1\Users\Http\Requests;

use App\Apis\V1\Base\Http\Requests\Request;

/**
 * @OA\Schema()
 */
class LoginRequest extends Request
{
    /**
     * @OA\Property(format="string", default="xingxiang@spacebib.com", description="email", property="email"),
     * @OA\Property(format="string", default="password", description="password", property="password"),
     */
    public function rules()
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }
}
