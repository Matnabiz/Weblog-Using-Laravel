<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Post;
use App\Exports\PostsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdminPostController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse{
        $posts = Post::with(['tags', 'likes'])->get();
        return response()->json($posts);
    }

    public function destroy($id){
        $post = Post::findOrFail($id);
        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }

    public function exportWeeklyPosts(){
        $firstPost = Post::orderBy('created_at', 'asc')->first();
        if (!$firstPost) {
            return response()->json(['error' => 'No posts available to export'], 404);
        }

        $startDate = Carbon::parse($firstPost->created_at)->startOfWeek();

        $currentDate = Carbon::now();

        while ($startDate->lessThanOrEqualTo($currentDate)) {
            $endDate = $startDate->clone()->endOfWeek();

            $fileName = 'posts-week-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.xlsx';

            Excel::store(new PostsExport($startDate, $endDate), $fileName);

            $startDate->addWeek();
        }

        return response()->json(['message' => 'Weekly post exports completed successfully']);
    }

}
