<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Http\Resources\PostResource;

class PostController extends Controller
{
    public function index(){
        // ambil posts
        $posts = Post::latest()->paginate(5);

        // kembalikan collection posts
        return new PostResource(true, 'List Data Post', $posts);
    }
}
