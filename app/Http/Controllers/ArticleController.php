<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        // Get articles with optional filtering by author
        $query = Article::query();

        if ($request->has('author')) {
            $query->where('author', $request->author);
        }

        $articles = $query->paginate(25);

        return $this->apiResponse(true, ArticleResource::collection($articles), 'Articles retrieved successfully.');

    }

    public function store(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'title' => 'required|string',
                'content' => 'required|string|min:50',
                'author' => 'required|string',
            ]);

            // Create the article
            $article = Article::create($validated);

            return $this->apiResponse(true, new ArticleResource($article), 'Article created successfully.', 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Catch validation errors
            return $this->apiResponse(false, $e->errors(), 'Validation Error', 422);
        } catch (\Exception) {
            // Catch all other errors
            return $this->apiResponse(false, null, 'Internal Server Error', 500);
        }
    }

    public function show($id)
    {
        try {
            $article = Article::findOrFail($id);

            return $this->apiResponse(true, new ArticleResource($article), 'Article retrieved successfully.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return $this->apiResponse(false, null, 'Resource not found', 404);
        } catch (\Exception) {
            return $this->apiResponse(false, null, 'Internal Server Error', 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $article = Article::findOrFail($id);

            // Validate input
            $validated = $request->validate([
                'title' => 'sometimes|required|string',
                'content' => 'sometimes|required|string|min:50',
                'author' => 'sometimes|required|string',
            ]);

            // Update the article
            $article->update($validated);

            return $this->apiResponse(true, new ArticleResource($article), 'Article updated successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->apiResponse(false, $e->errors(), 'Validation Error', 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return $this->apiResponse(false, null, 'Resource not found', 404);
        } catch (\Exception) {
            return $this->apiResponse(false, null, 'Internal Server Error', 500);
        }
    }

    public function destroy($id)
    {
        try {
            $article = Article::findOrFail($id);

            $article->delete();

            return $this->apiResponse(true, null, 'Article deleted successfully.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return $this->apiResponse(false, null, 'Resource not found', 404);
        } catch (\Exception) {
            return $this->apiResponse(false, null, 'Internal Server Error', 500);
        }
    }
}
