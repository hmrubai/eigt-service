<?php

namespace App\Services;

use App\Traits\HelperTrait;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
/**
 * Class SubjectService.
 */
class SubjectService
{
    use HelperTrait;

    public function allSubject(): mixed
    {
        $subject = Subject::select('subjects.*', 'categories.name_en as class_name_en', 'categories.name_bn as class_name_bn')
        ->leftJoin('categories', 'categories.id', 'subjects.category_id')
        ->where('subjects.status', 1)
        ->get();
        return $subject;
    }

    public function subjectListByCategoryID($category_id): mixed
    {
        $subject = Subject::select('subjects.*', 'categories.name_en as class_name_en', 'categories.name_bn as class_name_bn')
        ->leftJoin('categories', 'categories.id', 'subjects.category_id')
        ->where('subjects.status', 1)
        ->where('subjects.category_id', $category_id)
        ->get();
        return $subject;
    }

    public function saveSubject(Request $request): mixed
    {
        if(!$request->name_bn){
            throw ValidationException::withMessages(['Enter Subject Name!.']);
        }

        $subject = Subject::where("name_bn", $request->name_bn)->first();
        if (!empty($subject)) {
            throw ValidationException::withMessages(['Subject Name Already Exists!.']);
        }

        DB::beginTransaction();

        try {

            $thumbnail = $this->fileUpload($request, 'thumbnail', 'subject_thumbnail');

            $subject_data = [
                'name_bn' => $request->name_bn,
                'name_en' => $request->name_en,
                'subject_code' => $request->subject_code,
                'category_id' => $request->class_id,
                'thumbnail' => $thumbnail ?? '',
                'status' => is_null($request->status) ? 0 : $request->status,
                'created_by' => $request->jwt_user['id'] ?? 1,
            ];

            $inserted_subject = Subject::create($subject_data);

            DB::commit();
            return $inserted_subject;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function updateSubject(Request $request, int $id): mixed
    {
        if(!$request->name_bn){
            throw ValidationException::withMessages(['Enter Subject Name!.']);
        }

        $subject = Subject::findOrFail($id);

        $other_subject_exist = Subject::where("name_bn", $request->name_bn)->where("id", '!=', $id)->first();
        if (!empty($other_subject_exist)) {
            throw ValidationException::withMessages(['Subject Name Already Exists!.']);
        }

        $subject_data = [
            'name_bn' => $request->name_bn,
            'name_en' => $request->name_en,
            'subject_code' => $request->subject_code,
            'category_id' => $request->class_id,
            'status' => is_null($request->status) ? 0 : $request->status,
            'created_by' => $request->jwt_user['id'] ?? 1,
        ];

        if($request->thumbnail){
            $thumbnail = $this->fileUpload($request, 'thumbnail', 'subject_thumbnail');
            if($subject->thumbnail){
                $this->deleteFile($subject->thumbnail);
            }

            $subject_data = [
                'name_bn' => $request->name_bn,
                'name_en' => $request->name_en,
                'subject_code' => $request->subject_code,
                'category_id' => $request->class_id,
                'thumbnail' => $thumbnail ?? '',
                'status' => is_null($request->status) ? 0 : $request->status,
                'created_by' => $request->jwt_user['id'] ?? 1,
            ];
        }

        $subject->update($subject_data);
        return $subject;
    }

    public function deleteSubject(int $id)
    {
        try {
            $subject = Subject::findOrFail($id);
            if($subject->thumbnail){
                $this->deleteFile($subject->thumbnail);
            }

            return Subject::findOrFail($id)->delete();
        } catch (\Exception $e) {
            throw $e;
        }
    }

}
