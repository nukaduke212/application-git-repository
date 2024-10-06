<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Like;
use Illuminate\Http\Request;
use Cloudinary;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    
    public function index() 
    {
        
        
        //ログインしているユーザーのIDを取得 
        $currentUserId = Auth::id(); // ログインしているユーザー以外のユーザーを取得 
        $users = User::where('id', '!=', $currentUserId)->get();
        // 投稿をすべて取得 
        $posts = Post::all(); 
        // ビューにデータを渡して表示 
        return view('posts.index', ['posts' => $posts,'users' => $users]); 
        
    }
    
    public function create() 
    {
        return view('posts.create' ); 
        
    }
    
    public function show(Post $post)
    {
        // コメントを含めて投稿を取得 
        $post->load(['comments','likes']);
        return view('posts.show')->with(['post' => $post]);
    }

    public function store(Post $post, Request $request)
    {
        $input = $request['post'];
        //cloudinaryへ画像を送信し、画像のURLを$image_urlに代入している
        $image_url = Cloudinary::upload($request->file('image')->getRealPath())->getSecurePath();
        $input += ['image_path' => $image_url];
        $post->fill($input)->save();
        return redirect('/posts/' . $post->id);
    }
    
    public function edit(Post $post)
    {
        return view('posts.edit')->with(['post' => $post]);
    }
    
    public function update(PostRequest $request, Post $post)
    {
        $input_post = $request['post'];
        $post->fill($input_post)->save();
    
        return redirect('/posts/' . $post->id);
    }
    
    public function delete(Post $post)
    {
        $post->delete();
        return redirect('/');
    }
    
    public function storeComment(Request $request, $postId) 
    { 
        $request->validate([ 
            'body' => 'required|string|max:200',
            ]);
            
        $post = Post::findOrFail($postId);
        
        $post->comments()->create([
            'body' => $request->body, 
            'user_id' => Auth::id(), // ログイン中のユーザーID
        ]);
        return redirect()->back()->with('success', 'コメントが追加されました！'); 
        
    }
    
    public function likePost($postId)
    {
        $post = Post::findOrFail($postId);
        $like = Like::withTrashed()->where('post_id', $postId)->where('user_id', Auth::id())->first();
        if ($like && !$like->trashed()) {
            // 既に「いいね」している場合は取り消す 
            $like->delete(); 
            return redirect()->back()->with('success', 'いいねを取り消しました');
            } else { 
                if ($like && $like->trashed()) {
                    // ソフトデリートされた「いいね」を復元する 
                    $like->restore();
                    } else {
                        // 新規に「いいね」を作成する 
                        Like::create([
                            'post_id' => $postId,
                            'user_id' => Auth::id(),
                        ]);
                    }
                    return redirect()->back()->with('success', 'いいねしました');
                    }
                }
    
    public function search(Request $request)
    {
        $posts = Post::where('title', 'like', "%{$request->search}%")
                ->orwhere('body', 'like', "%{$request->search}%")
                ->paginate(5);
        $currentUserId = Auth::id();
        
         $users = User::where('id', '!=', $currentUserId)->get();
        $search_result = $request->search.'の検索結果'.count($posts).'件';
        
        return view('posts.index', ['posts' => $posts,'users' => $users]);
    }
}
