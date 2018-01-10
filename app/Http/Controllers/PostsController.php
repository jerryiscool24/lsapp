<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Post;

class PostsController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::orderBy('created_at', 'desc')->paginate(3);
        return view('posts.index')->with('posts', $posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
    
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'body'  =>  'required',
            'cover_image' => 'image|nullable|max:1999'
        ]);
        
        // Handle File Upload
        if($request->hasFile('cover_image'))
        {
            //Get Filename with extension
            $filenameWithExt = $request->file('cover_image')->getClientOriginalName();
            //Get just Filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            //Get just extension
            $extension = $request->file('cover_image')->getClientOriginalExtension();
            //Filename to store
            $filenameToStore = $filename.'_'.time().'.'.$extension;
            //Upload Image
            $path = $request->file('cover_image')->storeAs('public/cover_image', $filenameToStore);

        }
        else
        {
            $filenameToStore = 'noimage.jpg';
        }

        // Create Post
        
        $post = new Post();

        $post->title = $request->title;
        $post->body  = $request->body;
        $post->user_id = auth()->user()->id;
        $post->cover_image = $filenameToStore;
        $post->save();
        
        return redirect('/posts')->with('success', 'Success'); 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id);
        return view('posts.show')->with('post', $post);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Post::find($id);
      
        if(auth()->user()->id !== $post->user_id){
            return redirect('posts')->with('error', 'Unauthorized Page');
        }
            return view('posts.edit')->with('post', $post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'body'  =>  'required',
            'cover_image' => 'image|nullable|max:1999'
        ]);

        // Handle File Upload
        if($request->hasFile('cover_image'))
        {
            //Get Filename with extension
            $filenameWithExt = $request->file('cover_image')->getClientOriginalName();
            //Get just Filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            //Get just extension
            $extension = $request->file('cover_image')->getClientOriginalExtension();
            //Filename to store
            $filenameToStore = $filename.'_'.time().'.'.$extension;
            //Upload Image
            $path = $request->file('cover_image')->storeAs('public/cover_image', $filenameToStore);

        }

        // Create Post
        $post = Post::find($id);
        
        if(auth()->user()->id !== $post->user_id){
            return redirect('posts')->with('error', 'Unauthorized Page');
        }
        
        $post->title = $request->title;
        $post->body  = $request->body;
        if($request->hasFile('cover_image'))
        {
            if($post->cover_image != 'noimage.jpg')
            {
                Storage::delete('/public/cover_image/'.$post->cover_image);
            }

            $post->cover_image = $filenameToStore;
        }

        $post->save();
        return redirect('/posts')->with('success', 'Post Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);

        if(auth()->user()->id !== $post->user_id){
            return redirect('posts')->with('error', 'Unauthorized Page');
        }

        if($post->cover_image != 'noimage.jpg')
        {
            Storage::delete('/public/cover_image/'.$post->cover_image);
        }

        $post->delete();
        return redirect('/posts')->with('success', 'Post Removed');
    }
}
