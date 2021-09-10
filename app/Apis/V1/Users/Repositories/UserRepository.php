<?php
namespace App\Apis\V1\Users\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class UserRepository
 * @package App\Apis\V1\Users\Repositories
 */
class UserRepository
{
    /**
     * @param  int  $offset
     * @param  int  $limit
     * @return Collection
     */
    public function get(
        int $offset,
        int $limit
    ):Collection {
        return User::query()
            ->offset($offset ?? 0)
            ->limit($limit ?? 10)
            ->latest()
            ->get();
    }
}
