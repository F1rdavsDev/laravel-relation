<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PostStoreRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\PostCreateRequest;
use App\Http\Requests\PostUpdateRequest;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        $posts = Post::with('user')->get(); 
        return view("posts.index",compact("posts"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
     return view('posts.create');   
    }

    /**
     * 
     * 
     * Store a newly created resource in storage.
     */
    public function store(PostStoreRequest $request)
    {
       
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
        } else {
            $imagePath = null; 
        }
    
        Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'image' => $imagePath, 
            'user_id' => Auth::id(), 
        ]);
    
        return redirect()->route('posts.index');
    }
    
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::findOrFail($id);   
        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $post = Post::findOrFail($id);

    if (Auth::id() !== $post->user_id) {
        return redirect()->route('posts.index');
    }

    return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */ 
    public function update(PostUpdateRequest $request, string $id)
    {
        $post = Post::findOrFail($id);  
        
        if ($post->user_id !== auth()->id()) {
            return redirect()->route('posts.index');
        }
    if ($request->hasFile('image')) {
        if ($post->image && file_exists(public_path('storage/' . $post->image))) {
            unlink(public_path('storage/' . $post->image));  
        }
        $imagePath = $request->file('image')->store('images', 'public');
        $post->image = $imagePath;  
    }

    $post->update([
        'title' => $request->title,
        'content' => $request->content,
    ]);

    



        return redirect()->route('posts.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
{
    if (Auth::id() !== $post->user_id) {
        return redirect()->route('posts.index');
    }

    // Rasmni o‘chirish
    if ($post->image) {
        $imagePath = public_path('storage/' . $post->image);
        
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    $post->delete();

    return redirect()->route('posts.index');
}

    
}