<?php

namespace App\Http\Controllers;


use App\Services\SubjectService;
use Illuminate\Http\Request;
use App\Http\Requests\SubjectRequest;
use Illuminate\Http\Response;
use App\Traits\HelperTrait;

class SubjectController extends Controller
{
    use HelperTrait;

    protected $subjectService;

    public function __construct(SubjectService $subjectService) {
        $this->subjectService = $subjectService;
    }

    public function index()
    {
        $subject = $this->subjectService->allSubject();
        return $this->successResponse($subject, 'Subject Information', Response::HTTP_OK);
    }

    public function subjectListByCategory($category_id = 0)
    {
        $subject = $this->subjectService->subjectListByCategoryID($category_id);
        return $this->successResponse($subject, 'Subject Information', Response::HTTP_OK);
    }

    public function store(SubjectRequest $request)
    {
        try {
            $subject = $this->subjectService->saveSubject($request);
            return $this->successResponse($subject, 'Subject created successfully', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(SubjectRequest $request, $id)
    {
        try {
            $subject = $this->subjectService->updateSubject($request, $id);
            return $this->successResponse($subject, 'Subject updated successfully', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->subjectService->deleteSubject($id);

            return $this->successResponse([], 'Subject deleted successfully', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
