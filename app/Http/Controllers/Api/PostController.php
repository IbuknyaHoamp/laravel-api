<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Storage;
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

    public function update(Request $request, Post $post){
        // validasi inputan 
        $validator = Validator::make($request->all(), [
            'title'     => 'required',
            'content'   => 'required',
        ]);

        // cek jika validasi gagal
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // cek jika ada gambar
        if($request->hasFile('image')){
            // upload gambar
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            // hapus gambar lama
            Storage::delete('public/posts/' . $post->image);

            // update data
            $post->update([
                'image' => $image->hashName(),
                'title' => $request->title,
                'content' => $request->content
            ]);
        } else {
            $post->update([
                'title' => $request->title,
                'content' => $request->content
            ]);
        }

        // kembalikan response
        return new PostResource(true, "Data berhasil di update", $post);
    }

    public function destroy(Post $post){
        // hapus gambar
        Storage::delete('public/posts/' . $post->image);

        // hapus post
        $post->delete();

        // kembalikan response
        return new PostResource(true, "Data berhasil dihapus", null);
    }
}
