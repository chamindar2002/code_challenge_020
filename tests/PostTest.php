<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\UsersTableSeeder;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostTest extends TestCase
{

    use DatabaseMigrations;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {

        $faker = Faker\Factory::create();

        $this->artisan('db:seed');

        $this->seeInDatabase('users', ['email' => 'admin@gmail.com']);

        $user = User::find(1);

        $this->actingAs($user);

        /**
         * Test for validation
         */
        $response = $this->call('POST', 'api/v1/post', [
            'title'   => null,
            'content' => null,
            'tags'    => null
        ]);


        $response->assertJsonValidationErrors('title', $responseKey = null);
        $response->assertJsonValidationErrors('content', $responseKey = null);
        $response->assertJsonValidationErrors('title', $responseKey = null);

        /**
         * Test for creating a new post
         */
        $this->json(
            'POST',
            '/api/v1/post',
            [
                'title'   => $faker->sentence($nbWords = 6, $variableNbWords = true),
                'content' => $faker->text($maxNbChars = 200),
                'tags'    => $faker->words($nb = 2, $asText = false)
            ]
        )->seeJson([
            'status' => 'success',
        ]);

        /**
         * Test submitting a post to medium
         */
        $post = Post::where('user_id', $user->id)->latest()->first();

        $url = url("api/v1/post/{$post->uuid}/submit-to-medium");

        $this->json(
            'POST',
            $url,
            [
                'title'   => $faker->sentence($nbWords = 6, $variableNbWords = true),
                'content' => $faker->text($maxNbChars = 200),
                'tags'    => $faker->words($nb = 2, $asText = false)
            ]
        )->seeJson([
            'status' => 'success',
        ]);
    }
}
