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

            $category_date = [
                'name_bn' => $request->name_bn,
                'name_en' => $request->name_en,
                'thumbnail' => $thumbnail ?? '',
                'status' => is_null($request->status) ? 0 : $request->status,
                'created_by' => $request->jwt_user['id'] ?? 1,
            ];

            $category = Category::create($category_date);

            DB::commit();
            return $category;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
