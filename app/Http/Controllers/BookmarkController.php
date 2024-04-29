<?php

namespace App\Http\Controllers;


use App\Services\BookmarkService;
use Illuminate\Http\Request;
use App\Http\Requests\BookmarkRequest;
use App\Models\Bookmark;
use Illuminate\Http\Response;
use App\Traits\HelperTrait;

class BookmarkController extends Controller
{
    use HelperTrait;

    protected $bookmarkService;

    public function __construct(BookmarkService $bookmarkService) {
        $this->bookmarkService = $bookmarkService;
    }

    public function store(BookmarkRequest $request)
    {
        try {
            $bookmark = $this->bookmarkService->addRemoveBookmark($request);
            return $this->successResponse($bookmark, 'Bookmarked has been added successfully', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function bookmarkList(Request $request)
    {
        $bookmark = $this->bookmarkService->allBookmarkList($request);
        return $this->successResponse($bookmark, 'Bookmark List', Response::HTTP_OK);
    }
}
