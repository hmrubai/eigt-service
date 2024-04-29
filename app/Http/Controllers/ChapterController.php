<?php

namespace App\Http\Controllers;

use App\Services\ChapterService;
use App\Services\SubjectService;
use Illuminate\Http\Request;
use App\Http\Requests\ChapterRequest;
use Illuminate\Http\Response;
use App\Traits\HelperTrait;

class ChapterController extends Controller
{
    use HelperTrait;

    protected $chapterService;

    public function __construct(ChapterService $chapterService) {
        $this->chapterService = $chapterService;
    }

    public function index(Request $request)
    {
        $chapter = $this->chapterService->allChapter($request);
        return $this->successResponse($chapter, 'Chapter Information', Response::HTTP_OK);
    }

    public function chapterListBySubject($subject_id = 0, Request $request)
    {
        $chapter = $this->chapterService->chapterListBySubjectID($subject_id, $request);
        return $this->successResponse($chapter, 'Chapter Information', Response::HTTP_OK);
    }

    public function store(ChapterRequest $request)
    {
        try {
            $chapter = $this->chapterService->saveChapter($request);
            return $this->successResponse($chapter, 'Chapter created successfully', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(ChapterRequest $request, $id)
    {
        try {
            $chapter = $this->chapterService->updateChapter($request, $id);
            return $this->successResponse($chapter, 'Chapter updated successfully', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->chapterService->deleteChapter($id);

            return $this->successResponse([], 'Chapter deleted successfully', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
