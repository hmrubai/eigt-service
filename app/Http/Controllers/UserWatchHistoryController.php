<?php

namespace App\Http\Controllers;

use App\Services\UserWatchHistoryService;
use Illuminate\Http\Request;
use App\Http\Requests\UserWatchHistoryRequest;
use App\Models\UserWatchHistory;
use Illuminate\Http\Response;
use App\Traits\HelperTrait;

class UserWatchHistoryController extends Controller
{
    use HelperTrait;

    protected $watchHistoryService;

    public function __construct(UserWatchHistoryService $watchHistoryService) {
        $this->watchHistoryService = $watchHistoryService;
    }

    public function store(UserWatchHistoryRequest $request)
    {
        try {
            $watch_history = UserWatchHistory::where("user_id", $request->jwt_user['id'])->where("content_id", $request->content_id)->first();
            if (!empty($watch_history)) {
                return $this->deleteResponse('This Content is already been in watched list.', Response::HTTP_OK);
            }

            $watch_history_added = $this->watchHistoryService->addToWatchHistory($request);
            return $this->successResponse($watch_history_added, 'This content has beed added to Watched List.', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
