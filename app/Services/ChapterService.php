<?php

namespace App\Services;

use App\Traits\HelperTrait;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Subject;
use App\Models\Content;
use App\Models\Chapter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Class ChapterService.
 */
class ChapterService
{

    use HelperTrait;

    public function allChapter(): mixed
    {
        $chapter = Chapter::select('chapters.*', 'categories.name_en as class_name_en', 'categories.name_bn as class_name_bn', 
        'subjects.name_en as subject_name_en', 'subjects.name_bn as subject_name_bn')
        ->leftJoin('categories', 'categories.id', 'chapters.category_id')
        ->leftJoin('subjects', 'subjects.id', 'chapters.subject_id')
        ->where('chapters.status', 1)
        ->get();

        foreach ($chapter as $item) {
            $item->content = Content::where('status', 1)->get();
        }

        return $chapter;
    }

    public function chapterListBySubjectID($subject_id): mixed
    {
        $chapter = Chapter::select('chapters.*', 'categories.name_en as class_name_en', 'categories.name_bn as class_name_bn', 
        'subjects.name_en as subject_name_en', 'subjects.name_bn as subject_name_bn')
        ->leftJoin('categories', 'categories.id', 'chapters.category_id')
        ->leftJoin('subjects', 'subjects.id', 'chapters.subject_id')
        ->where('chapters.status', 1)
        ->where('chapters.subject_id', $subject_id)
        ->get();
        
        foreach ($chapter as $item) {
            $item->content = Content::where('status', 1)->get();
        }

        return $chapter;
    }

    public function saveChapter(Request $request): mixed
    {
        if(!$request->name_bn){
            throw ValidationException::withMessages(['Enter Chapter Name!.']);
        }

        $chapter = Chapter::where("name_bn", $request->name_bn)->first();
        if (!empty($chapter)) {
            throw ValidationException::withMessages(['Chapter Name Already Exists!.']);
        }

        DB::beginTransaction();

        try {

            $thumbnail = $this->fileUpload($request, 'thumbnail', 'chapter_thumbnail');

            $chapter_data = [
                'name_bn' => $request->name_bn,
                'name_en' => $request->name_en,
                'category_id' => $request->class_id,
                'subject_id' => $request->subject_id,
                'thumbnail' => $thumbnail ?? '',
                'status' => is_null($request->status) ? 0 : $request->status,
                'created_by' => $request->jwt_user['id'] ?? 1,
            ];

            $inserted_data = Chapter::create($chapter_data);

            DB::commit();
            return $inserted_data;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function updateChapter(Request $request, int $id): mixed
    {
        if(!$request->name_bn){
            throw ValidationException::withMessages(['Enter Chapter Name!.']);
        }

        $chapter = Chapter::findOrFail($id);

        $other_chapter_exist = Chapter::where("name_bn", $request->name_bn)->where("id", '!=', $id)->first();
        if (!empty($other_chapter_exist)) {
            throw ValidationException::withMessages(['Chapter Name Already Exists!.']);
        }

        $update_data = [
            'name_bn' => $request->name_bn,
            'name_en' => $request->name_en,
            'category_id' => $request->class_id,
            'subject_id' => $request->subject_id,
            'status' => is_null($request->status) ? 0 : $request->status,
            'created_by' => $request->jwt_user['id'] ?? 1,
        ];

        if($request->thumbnail){
            $thumbnail = $this->fileUpload($request, 'thumbnail', 'chapter_thumbnail');
            if($chapter->thumbnail){
                $this->deleteFile($chapter->thumbnail);
            }

            $update_data = [
                'name_bn' => $request->name_bn,
                'name_en' => $request->name_en,
                'category_id' => $request->class_id,
                'subject_id' => $request->subject_id,
                'thumbnail' => $thumbnail ?? '',
                'status' => is_null($request->status) ? 0 : $request->status,
                'created_by' => $request->jwt_user['id'] ?? 1,
            ];
        }

        $chapter->update($update_data);
        return $chapter;
    }

    public function deleteChapter(int $id)
    {
        try {
            $chapter = Chapter::findOrFail($id);
            if($chapter->thumbnail){
                $this->deleteFile($chapter->thumbnail);
            }

            return Chapter::findOrFail($id)->delete();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
