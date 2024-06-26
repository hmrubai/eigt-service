<?php

namespace App\Services;

use App\Traits\HelperTrait;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Subject;
use App\Models\Chapter;
use App\Models\Content;
use App\Models\Bookmark;
use App\Models\UserWatchHistory;
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

    public function contentListByChapterID($chapter_id, Request $request): mixed
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

        foreach ($content as $item) {
            $content->is_bookmarked = Bookmark::where('user_id', $request->jwt_user['id'])->where("content_id", $item->id)->first() ? true : false;
            $content->is_watched = UserWatchHistory::where('user_id', $request->jwt_user['id'])->where("content_id", $item->id)->first() ? true : false;
        }

        return $content;
    }

    public function contentDetailsByID($content_id, Request $request): mixed
    {
        $content = Content::select('contents.*', 'categories.name_en as class_name_en', 'categories.name_bn as class_name_bn', 
        'subjects.name_en as subject_name_en', 'subjects.name_bn as subject_name_bn', 
        'chapters.name_en as chapter_name_en', 'chapters.name_bn as chapter_name_bn')
        ->leftJoin('categories', 'categories.id', 'contents.category_id')
        ->leftJoin('subjects', 'subjects.id', 'contents.subject_id')
        ->leftJoin('chapters', 'chapters.id', 'contents.chapter_id')
        ->where('contents.id', $content_id)
        ->first();

        if($content){
            $content['is_bookmarked'] = Bookmark::where('user_id', $request->jwt_user['id'])->where("content_id", $content_id)->first() ? true : false;
            $content['is_watched'] = UserWatchHistory::where('user_id', $request->jwt_user['id'])->where("content_id", $content_id)->first() ? true : false;
        }

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

            $raw_file = $this->fileUpload($request, 'raw_file', 'raw_file');
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

            $inserted_data = Content::create($content_data);

            DB::commit();
            return $inserted_data;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function updateContent(Request $request, int $id): mixed
    {
        if(!$request->name_bn){
            throw ValidationException::withMessages(['Enter Content Title!.']);
        }

        $content = Content::findOrFail($id);

        $other_content_exist = Content::where("name_bn", $request->name_bn)->where("id", '!=', $id)->first();
        if (!empty($other_content_exist)) {
            throw ValidationException::withMessages(['Content Already Exists!.']);
        }

        if($request->thumbnail){
            if($content->thumbnail){
                $this->deleteFile($content->thumbnail);
                $thumbnail = $this->fileUpload($request, 'thumbnail', 'content_thumbnail');

                $update_data = [
                    'thumbnail' => $thumbnail ?? ''
                ];

                $content->update($update_data);
            }
        }

        if($request->raw_file){
            if($content->raw_file){
                $this->deleteFile($content->raw_file);
                $raw_file = $this->fileUpload($request, 'raw_file', 'raw_file');

                $update_data = [
                    'raw_file' => $raw_file ?? ''
                ];

                $content->update($update_data);
            }
        }

        $update_data = [
            'name_bn' => $request->name_bn,
            'name_en' => $request->name_en,
            'description' => $request->description,
            'category_id' => $request->class_id,
            'subject_id' => $request->subject_id,
            'chapter_id' => $request->chapter_id,
            'transcoded_file_path' => "https://bacbonschool.s3.ap-south-1.amazonaws.com/uploads/e-Teachers_Guide/Class_Five/English/The_Liberation_War_Museum/lectures/1410135322001/index.m3u8",
            'compressed_file_path' => "https://media.bacbonltd.net/videos/JICF/Class_Five/English/1410135322001.mp4",
            'content_type' => $request->content_type ?? 'Video',
            'status' => is_null($request->status) ? 0 : $request->status,
            'created_by' => $request->jwt_user['id'] ?? 1,
        ];

        $content->update($update_data);
        return $content;
    }

    public function deleteContent(int $id)
    {
        try {
            $content = Content::findOrFail($id);
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
