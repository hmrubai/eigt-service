<?php

namespace App\Services;

use App\Traits\HelperTrait;
use Illuminate\Http\Request;
use App\Models\Bookmark;
use App\Models\Content;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
/**
 * Class BookmarkService.
 */
class BookmarkService
{
    use HelperTrait;

    public function addRemoveBookmark(Request $request): mixed
    {
        DB::beginTransaction();

        try {
            $bookmark_data = [
                'user_id' => $request->jwt_user['id'],
                'content_id' => $request->content_id
            ];

            $bookmark_inserted = Bookmark::create($bookmark_data);

            DB::commit();
            return $bookmark_inserted;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function allBookmarkList(Request $request): mixed
    {
        $content_ids = Bookmark::where("user_id", $request->jwt_user['id'])->pluck('content_id');

        $content = Content::select('contents.*', 'categories.name_en as class_name_en', 'categories.name_bn as class_name_bn', 
        'subjects.name_en as subject_name_en', 'subjects.name_bn as subject_name_bn', 
        'chapters.name_en as chapter_name_en', 'chapters.name_bn as chapter_name_bn')
        ->leftJoin('categories', 'categories.id', 'contents.category_id')
        ->leftJoin('subjects', 'subjects.id', 'contents.subject_id')
        ->leftJoin('chapters', 'chapters.id', 'contents.chapter_id')
        ->where('chapters.status', 1)
        ->whereIn('contents.id', $content_ids)
        ->get();

        foreach ($content as $item) {
            $item->is_bookmarked = true;
        }

        return $content;
    }

}
