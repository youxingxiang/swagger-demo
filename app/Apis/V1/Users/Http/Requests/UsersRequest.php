<?php
namespace App\Apis\V1\Users\Http\Requests;

use App\Apis\V1\Base\Http\Requests\Request;

class UsersRequest extends Request
{
    public function rules()
    {
        return [
            'offset' => 'required|integer',
            'limit' => 'required|string',
        ];
    }
}
