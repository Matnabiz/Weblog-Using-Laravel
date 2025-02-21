<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Post;

class PostCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_post_happy_path()
    {
        $data = [
            'title' => 'Test Post',
            'content' => 'This is a test post content',
        ];

        $response = $this->post(route('posts.store'), $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post',
            'content' => 'This is a test post content',
        ]);
    }

    public function test_read_post_happy_path()
    {
        $post = Post::factory()->create([
            'title' => 'Test Post',
            'content' => 'This is a test post content',
        ]);
    
        $response = $this->get(route('posts.show', $post->id));
    
        $response->assertStatus(200);
    
        $response->assertJson([
            'title' => 'Test Post',
            'content' => 'This is a test post content',
        ]);
    }

    public function test_update_post_happy_path()
    {
        $post = Post::factory()->create([
            'title' => 'Old Title',
            'content' => 'Old content',
        ]);
    
        $updatedData = [
            'title' => 'Updated Title',
            'content' => 'Updated content',
        ];
    
        $response = $this->put(route('posts.update', $post->id), $updatedData);
    
        $response->assertStatus(200);
    
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title',
            'content' => 'Updated content',
        ]);
    }

    public function test_delete_post_happy_path()
    {
        $post = Post::factory()->create();
    
        $response = $this->delete(route('posts.destroy', $post->id));
    
        $response->assertStatus(204);
    
        $this->assertDeleted($post);
    }
    
    
}