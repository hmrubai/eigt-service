<?php

namespace App\Services;

use App\Traits\HelperTrait;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Subject;
use App\Models\Chapter;
use App\Models\Content;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Class ContentService.
 */
class ContentService
{
    use HelperTrait;

    public function allContent(): mixed
    {
        $content = Content::select('contents.*', 'categories.name_en as class_name_en', 'categories.name_bn as class_name_bn', 
        'subjects.name_en as subject_name_en', 'subjects.name_bn as subject_name_bn', 
        'chapters.name_en as chapter_name_en', 'chapters.name_bn as chapter_name_bn')
        ->leftJoin('categories', 'categories.id', 'contents.category_id')
        ->leftJoin('subjects', 'subjects.id', 'contents.subject_id')
        ->leftJoin('chapters', 'chapters.id', 'contents.chapter_id')
        ->where('chapters.status', 1)
        ->get();
        return $content;
    }

    public function contentListByChapterID($chapter_id): mixed
    {
        $content = Content::select('contents.*', 'categories.name_en as class_name_en', 'categories.name_bn as class_name_bn', 
        'subjects.name_en as subject_name_en', 'subjects.name_bn as subject_name_bn', 
        'chapters.name_en as chapter_name_en', 'chapters.name_bn as chapter_name_bn')
        ->leftJoin('categories', 'categories.id', 'contents.category_id')
        ->leftJoin('subjects', 'subjects.id', 'contents.subject_id')
        ->leftJoin('chapters', 'chapters.id', 'contents.chapter_id')
        ->where('chapters.status', 1)
        ->where('chapters.subject_id', $chapter_id)
        ->get();
        return $content;
    }

    public function adminContentListByChapterID($chapter_id): mixed
    {
        $content = Content::select('contents.*', 'categories.name_en as class_name_en', 'categories.name_bn as class_name_bn', 
        'subjects.name_en as subject_name_en', 'subjects.name_bn as subject_name_bn', 
        'chapters.name_en as chapter_name_en', 'chapters.name_bn as chapter_name_bn')
        ->leftJoin('categories', 'categories.id', 'contents.category_id')
        ->leftJoin('subjects', 'subjects.id', 'contents.subject_id')
        ->leftJoin('chapters', 'chapters.id', 'contents.chapter_id')
        ->where('chapters.subject_id', $chapter_id)
        ->get();
        return $content;
    }

    public function saveContent(Request $request): mixed
    {
        if(!$request->name_bn){
            throw ValidationException::withMessages(['Enter Content Title!.']);
        }

        $content = Content::where("name_bn", $request->name_bn)->where("chapter_id", $request->chapter_id)->first();
        if (!empty($content)) {
            throw ValidationException::withMessages(['Content Already Exists!.']);
        }

        DB::beginTransaction();

        try {

            $thumbnail = $this->fileUpload($request, 'raw_file', 'raw_file');
            $thumbnail = $this->fileUpload($request, 'thumbnail', 'content_thumbnail');

            $content_data = [
                'name_bn' => $request->name_bn,
                'name_en' => $request->name_en,
                'description' => $request->description,
                'category_id' => $request->class_id,
                'subject_id' => $request->subject_id,
                'chapter_id' => $request->chapter_id,
                'raw_file' => $raw_file ?? '',
                'thumbnail' => $thumbnail ?? '',
                'transcoded_file_path' => "https://bacbonschool.s3.ap-south-1.amazonaws.com/uploads/e-Teachers_Guide/Class_Five/English/The_Liberation_War_Museum/lectures/1410135322001/index.m3u8",
                'compressed_file_path' => "https://media.bacbonltd.net/videos/JICF/Class_Five/English/1410135322001.mp4",
                'content_type' => $request->content_type ?? 'Video',
                'status' => is_null($request->status) ? 0 : $request->status,
                'created_by' => $request->jwt_user['id'] ?? 1,
            ];

            $inserted_data = Chapter::create($content_data);

            DB::commit();
            return $inserted_data;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function deleteContent(int $id)
    {
        try {
            $content = Chapter::findOrFail($id);
            if($content->thumbnail){
                $this->deleteFile($content->thumbnail);
            }

            $this->deleteFile($content->raw_file);

            return content::findOrFail($id)->delete();
        } catch (\Exception $e) {
            throw $e;
        }
    }

}
