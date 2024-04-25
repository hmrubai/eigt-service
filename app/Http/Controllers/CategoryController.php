<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Response;
use App\Traits\HelperTrait;


class CategoryController extends Controller
{
    use HelperTrait;

    protected $categoryService;

    public function __construct(CategoryService $categoryService) {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $category = $this->categoryService->allCategory();
        return $this->successResponse($category, 'Class Information', Response::HTTP_OK);
    }

    public function store(CategoryRequest $request)
    {
        try {
            $category = $this->categoryService->saveCategory($request);
            return $this->successResponse($category, 'Class created successfully', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(CategoryRequest $request, $id)
    {
        try {
            $category = $this->categoryService->updateCategory($request, $id);
            return $this->successResponse($category, 'Class updated successfully', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->categoryService->deleteCategory($id);

            return $this->successResponse([], 'Class deleted successfully', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
