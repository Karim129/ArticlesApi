<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Article;

class ArticleApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_an_article()
    {
        $response = $this->postJson('/api/articles', [
            'title' => 'Sample Article',
            'content' => 'This is the content of the article, which is more than 50 characters.',
            'author' => 'John Doe',
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Article created successfully.',
                 ]);

        $this->assertDatabaseHas('articles', ['title' => 'Sample Article']);
    }

    public function test_can_list_all_articles()
    {
        Article::factory(10)->create();

        $response = $this->getJson('/api/articles');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ])
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'title', 'content', 'author', 'created_at', 'updated_at'],
                     ],
                     'message',
                 ]);
    }

    public function test_can_filter_articles_by_author()
    {
        Article::factory()->create(['author' => 'Jane Doe']);
        Article::factory()->create(['author' => 'John Doe']);

        $response = $this->getJson('/api/articles?author=Jane Doe');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ])
                 ->assertJsonFragment(['author' => 'Jane Doe']);
    }

    public function test_can_get_specific_article()
    {
        $article = Article::factory()->create();

        $response = $this->getJson("/api/articles/{$article->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $article->id,
                         'title' => $article->title,
                         'content' => $article->content,
                         'author' => $article->author,
                     ],
                     'message' => 'Article retrieved successfully.',
                 ]);
    }

    public function test_can_update_an_article()
    {
        $article = Article::factory()->create();

        $response = $this->putJson("/api/articles/{$article->id}", [
            'title' => 'Updated Title',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Article updated successfully.',
                 ]);

        $this->assertDatabaseHas('articles', ['title' => 'Updated Title']);
    }

    public function test_can_delete_an_article()
    {
        $article = Article::factory()->create();

        $response = $this->deleteJson("/api/articles/{$article->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Article deleted successfully.',
                 ]);

        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    public function test_cannot_create_article_with_invalid_data()
    {
        $response = $this->postJson('/api/articles', [
            'title' => '',
            'content' => 'Short content.',
            'author' => '',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['title', 'content', 'author']);
    }
}
