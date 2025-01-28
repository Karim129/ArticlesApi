<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArticleResource;
use App\Models\Article;
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
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string|min:50',
            'author' => 'required|string',
        ]);

        $article = Article::create($request->all());

        return $this->apiResponse(true, new ArticleResource($article), 'Article created successfully.');

    }

    public function show(Article $article)
    {

        return $this->apiResponse(true, new ArticleResource($article), 'Article retrieved successfully.');

    }

    public function update(Request $request, Article $article)
    {
        $request->validate([
            'title' => 'sometimes|required|string',
            'content' => 'sometimes|required|string|min:50',
            'author' => 'sometimes|required|string',
        ]);

        $article->update($request->all());

        return $this->apiResponse(true, new ArticleResource($article), 'Article updated successfully.');

    }

    public function destroy(Article $article)
    {
        $article->delete();

        return $this->apiResponse(true, [], 'Article deleted successfully.');

    }
}
