<?php

namespace App\Services;


use App\Traits\HelperTrait;
use Illuminate\Http\Request;
use App\Models\UserWatchHistory;
use Illuminate\Support\Facades\DB;

/**
 * Class UserWatchHistoryService.
 */
class UserWatchHistoryService
{
    use HelperTrait;

    public function addToWatchHistory(Request $request): mixed
    {
        DB::beginTransaction();

        try {
            $history_data = [
                'user_id' => $request->jwt_user['id'],
                'content_id' => $request->content_id
            ];

            $bookmark_inserted = UserWatchHistory::create($history_data);

            DB::commit();
            return $bookmark_inserted;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

}
