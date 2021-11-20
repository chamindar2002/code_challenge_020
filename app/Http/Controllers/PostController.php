<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\PostToMediumJob;
use App\Models\Image;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Upload Image
     * 
     * @urlParam String $postId
     */
    public function uploadImage(Request $request, $uuid)
    {

        $post = Post::where('uuid', $uuid)->first();

        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        $this->validate($request, [
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        try {

            $uploadedFileName = $request->file->getClientOriginalName();
            $uploadedFileName = uniqid() . "." . $request->file->getClientOriginalExtension();
            $destination = 'uploads' . DIRECTORY_SEPARATOR . 'posts' . DIRECTORY_SEPARATOR;
            $request->file('file')->move($destination, $uploadedFileName);

            $image = new Image();
            $image->uuid    = Str::uuid();
            $image->post_id = $post->id;
            $image->url     = $destination . $uploadedFileName;

            if ($image->save()) {
                return response()->json(
                    [
                        'status'    => 'success',
                        'image_url' => url($destination . $uploadedFileName),
                        'message'   => 'Image Uploaded Successfully'
                    ]
                );
            }
        } catch (\Exception $e) {

            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }

        return response()->json(['status' => 'error', 'message' => 'Something went wrong']);
    }
}
