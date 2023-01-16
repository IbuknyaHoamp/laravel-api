<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index(){
        // ambil posts
        $posts = Post::latest()->paginate(5);

        // kembalikan collection posts
        return new PostResource(true, 'List Data Post', $posts);
    }

    public function store(Request $request){
        // rules validasi
        $validator = Validator::make($request->all(), [
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'     => 'required',
            'content'   => 'required',
        ]);

        // cek jika inputan gagal
        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        // upload gambar
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        // buat post
        $post = Post::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content
        ]);

        // kembalikan status data dan message
        return new PostResource(true, 'Data berhasil ditambahkan', $post);
    }

    public function show(Post $post){
        // kembalikan data single post
        return new PostResource(true, 'Data post ditemukan!', $post);
    }
}
