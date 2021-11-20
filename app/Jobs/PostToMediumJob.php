<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PostToMediumJob extends Job
{

    /**
     * @var App/Models/Post
     */
    private $post;

    /**
     * @var App/Models/User
     */
    private $user;

    /**
     * Create a new job instance.
     * 
     * @param App/Models/Post
     * @param App/Models/User
     *
     * @return void
     */
    public function __construct($post, $user)
    {
        $this->post = $post;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::debug('Post to medium job invoked');

        /**
         * fetch author id
         */
        if ($this->user->medium_author_id == null) {

            try {

                $response = Http::withToken($this->user->medium_integration_token)->get('https://api.medium.com/v1/me');

                if ($response->successful()) {

                    $responseData = $response->json('data');

                    if (!empty($responseData)) {
                        $this->user->medium_author_id = $responseData['id'];
                        $this->user->update();
                    }
                }
            } catch (Exception $e) {
                Log::error($e);
            }
        }

        /**
         * push post to medium
         */
        try {

            $uri = "https://api.medium.com/v1/users/{$this->user->medium_author_id}/posts";

            $response = Http::withToken($this->user->medium_integration_token)
                ->post($uri, [
                    "title" => $this->post->title,
                    "contentFormat" => "html",
                    "content" => $this->post->content,
                    "tags" => json_decode($this->post->tags),
                    "publishStatus" => "draft"

                ]);

            if ($response->successful()) {

                $responseData = $response->json('data');

                if (!empty($responseData)) {
                    $this->post->medium_response_payload = json_encode($responseData);

                    if ($this->post->save()) {
                        Log::debug('Posted to medium successfully '. $responseData['id']);
                    }
                }
            }
        } catch (Exception $e) {
            
            Log::error($e);
        }

    }
}
