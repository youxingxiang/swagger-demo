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
