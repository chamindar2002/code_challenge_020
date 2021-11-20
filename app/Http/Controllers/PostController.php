<?php

namespace App\Http\Controllers;

use App\Jobs\PostToMediumJob;
use Exception;
use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PostController extends Controller
{
    /**
     * Add new Post
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title'  => 'required|max:200',
            'content' => 'required',
            'tags' => 'required|array|min:1'
        ]);

        try {

            $post = new Post();
            $post->uuid  = Str::uuid();
            $post->user_id = \Auth::user()->id;
            $post->title = $request->title;
            $post->content = $request->content;
            $post->tags = json_encode($request->tags);

            if ($post->save()) {
                return response()->json(
                    [
                        'status' => 'success',
                        'post_id' => $post->uuid,
                        'message' => 'Post Created Successfully'
                    ]
                );
            }
        } catch (\Exception $e) {

            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Submit Post To Medium
     * 
     * @urlParam uuid required
     */
    public function submitToMedium(Request $request, $uuid)
    {

        $post = Post::where('uuid', $uuid)->first();

        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        dispatch(
            new PostToMediumJob(
                $post,
                \Auth::user()
            )
        );

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Successfully Queued'
            ]
        );
    }
}
