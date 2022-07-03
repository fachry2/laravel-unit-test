<?php

namespace App\Http\Controllers;

use App\Author;
use App\Book;
use App\Http\Requests\PostBookRequest;
use App\Http\Resources\BookResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class BooksController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        // @TODO implement
        $direction = 'ASC';
        $book      = Book::query();
        if ($request->has('title')) {
            $book->where('title', 'like', "%$request->title%");
        }
        if ($request->has('authors')) {
            $book->whereHas('authors', function (Builder $query) use ($request) {
                $query->whereIn('id', explode(',', $request->authors));
            });
        }
        if ($request->has('sortColumn')) {
            if ($request->has('sortDirection')) {
                $direction = $request->sortDirection;
            }
            if ($request->sortColumn !== 'avg_review') {
                $book->orderBy($request->sortColumn, $direction);
            } else {
            }
        }
        $books = $book->paginate(15);
        if ($request->has('sortColumn') && $request->sortColumn === 'avg_review') {
            if ($direction === 'ASC') {
                $books = $books->sortBy(function ($query) {
                    return $query->reviews->avg('review');
                })->all();
            } else {
                $books = $books->sortByDesc(function ($query) {
                    return $query->reviews->avg('review');
                })->all();
            }
        }

        return BookResource::collection($books);
    }

    public function store(PostBookRequest $request)
    {
        // @TODO implement
        $author               = Author::whereIn('id', $request['authors'])->first();
        $book = new Book();
        $book->isbn           = $request['isbn'];
        $book->title          = $request['title'];
        $book->description    = $request['description'];
        $book->published_year = $request['published_year'];
        $book->save();
        $book->authors()->saveMany([
            $author,
        ]);

        return new BookResource($book);
    }
}
