<?php

namespace App\Http\Controllers;

use App\Services\ContentService;
use App\Services\ChapterService;
use App\Services\SubjectService;
use Illuminate\Http\Request;
use App\Http\Requests\ChapterRequest;
use App\Http\Requests\ContentRequest;
use Illuminate\Http\Response;
use App\Traits\HelperTrait;


class ContentController extends Controller
{
    use HelperTrait;

    protected $contentService;

    public function __construct(ContentService $contentService) {
        $this->contentService = $contentService;
    }

    public function index()
    {
        $content = $this->contentService->allContent();
        return $this->successResponse($content, 'Content Information', Response::HTTP_OK);
    }

    public function contentListByChapter($chapter_id = 0)
    {
        $content = $this->contentService->contentListByChapterID($chapter_id);
        return $this->successResponse($content, 'Content Information', Response::HTTP_OK);
    }

    public function store(ChapterRequest $request)
    {
        try {
            $content = $this->contentService->saveContent($request);
            return $this->successResponse($content, 'Content created successfully', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->contentService->deleteContent($id);

            return $this->successResponse([], 'Content deleted successfully', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
