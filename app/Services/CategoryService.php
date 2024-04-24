<?php

namespace App\Services;

use App\Traits\HelperTrait;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Class CategoryService.
 */
class CategoryService
{
    use HelperTrait;

    public function allCategory(): mixed
    {
        $category = Category::get();
        return $category;
    }

    public function saveCategory(Request $request): mixed
    {
        if(!$request->name_bn){
            throw ValidationException::withMessages(['Enter Class Name!.']);
        }

        $category = Category::where("name_bn", $request->name_bn)->first();
        if (!empty($category)) {
            throw ValidationException::withMessages(['Class Name Already Exists!.']);
        }

        DB::beginTransaction();

        try {

            $thumbnail = $this->fileUpload($request, 'thumbnail', 'class_thumbnail');

            $category_data = [
                'name_bn' => $request->name_bn,
                'name_en' => $request->name_en,
                'thumbnail' => $thumbnail ?? '',
                'status' => is_null($request->status) ? 0 : $request->status,
                'created_by' => $request->jwt_user['id'] ?? 1,
            ];

            $category = Category::create($category_data);

            DB::commit();
            return $category;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function updateCategory(Request $request, int $id): mixed
    {
        if(!$request->name_bn){
            throw ValidationException::withMessages(['Enter Class Name!.']);
        }

        $category = Category::findOrFail($id);

        $other_category_exist = Category::where("name_bn", $request->name_bn)->where("id", '!=', $id)->first();
        if (!empty($other_category_exist)) {
            throw ValidationException::withMessages(['Class Name Already Exists!.']);
        }

        $category_data = [
            'name_bn' => $request->name_bn,
            'name_en' => $request->name_en,
            'status' => is_null($request->status) ? 0 : $request->status,
            'created_by' => $request->jwt_user['id'] ?? 1,
        ];

        if($request->thumbnail){
            $thumbnail = $this->fileUpload($request, 'thumbnail', 'class_thumbnail');

            $category_data = [
                'name_bn' => $request->name_bn,
                'name_en' => $request->name_en,
                'thumbnail' => $thumbnail ?? '',
                'status' => is_null($request->status) ? 0 : $request->status,
                'created_by' => $request->jwt_user['id'] ?? 1,
            ];
        }

        $category->update($category_data);
        return $category;
    }

    public function deleteCategory(int $id)
    {
        try {
            return Category::findOrFail($id)->delete();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
