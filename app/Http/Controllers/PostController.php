<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(){
        //get posts
        $posts = Post::latest()->paginate(5);
        
        //render posts
        return view('posts.index', compact('posts'));
    }

    public function create() {
        return view('posts.create');
    }

    public function store(Request $request){
        //validate form
        $this->validate($request,[
            'image' => 'required|image|mimes:jpeg,png,gif,svg|max:2048',
            'title' => 'required|min:5',
            'content' => 'required|min:10'
        ]);
        //upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashname());

        //create post
        post::create([
            'image' => $image->hashname(),
            'title' =>$request->title,
            'content' => $request->content
        ]);
        return redirect()->route('posts.index')->with(['succes' => 'Data berhasil disimpan']);
    }
    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }
    public function update(Request $request, Post $post)
    {
        //validate form
        $this->validate($request,[
            'image' => 'required|image|mimes:jpeg,png,gif,svg|max:2048',
            'title' => 'required|min:5',
            'content' => 'required|min:10'
        ]);
        if ($request->hasFile('image')) {

        
        //upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashname());
        Storage::delete('public/posts/'.$post->image);

        //create post
        $post->update([
            'image' => $image->hashname(),
            'title' =>$request->title,
            'content' => $request->content
        ]);
        } else {
            $post->update([
                'title' => $request->title,
                'content' => $request->content
            ]);
        }
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Diubah!']);
        
}
public function destroy(Post $post)
{
    Storage::delete('public/posts/'. $post->image);

    $post->delete();
    return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Dihapus!']);
}
public function show(string $id)
{
    $post = Post::findOrFail($id);
    return view('posts.show', compact('post'));
}
}
