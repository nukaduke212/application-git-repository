<!DOCTYPE html>
<script src="https://kit.fontawesome.com/4b4f952461.js" crossorigin="anonymous"></script>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <x-app-layout>
    <x-slot name="header">
    <head>
        <meta charset="utf-8">
        <title>Blog</title>
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    </head>
    </x-slot>
    <body class="antialiased">
        <h1 class='title'>
            {{ $post->title }}
        </h1>
        <div class='content'>
            <div class='content_post'>
                
                <h3>本文</h3>
                <p class='body'>{{ $post->body }}</p>
            </div>
            <img src="{{ asset($post->image_path) }}" alt="Post Image" width="600" height="400">
        </div>
            <div class="edit">
                <a href="/posts/{{ $post->id }}/edit">edit</a>
            </div>
            <!-- コメント表示セクション -->
        <div class="comments">
            <h3>コメント</h3>
            @foreach ($post->comments as $comment)
                <div class="comment">
                    <strong>{{ $comment->user->name }}</strong>
                    <p>{{ $comment->body }}</p>
                    <small>{{ $comment->created_at->diffForHumans() }}</small>
                </div>
            @endforeach
        </div>
        <!-- いいねボタン -->
        <form action="{{ route('posts.like', $post->id) }}" method="POST">
            @csrf
            @if($post->likes->where('user_id', Auth::id())->count() > 0)
                <button type="submit"><i class="fa-solid fa-heart"></i></button>
            @else
                <button type="submit"><i class="fa-regular fa-heart"></i></button>
            @endif
            <span>{{ $post->likes->count() }} いいね</span>
        </form>
        <!-- コメント入力フォーム -->
        <div class="comment-form">
            <h3>コメントを残す</h3>
            <form action="{{ route('posts.comments.store', $post->id) }}" method="POST"> 
                @csrf 
                <div> 
                    <label for="body">コメント:</label> 
                    <textarea name="body" id="body" rows="5" required></textarea> 
                </div>
                <button type="submit">コメントを投稿</button> 
            </form>
        </div>
        <div class="footer">
            <a href="/">戻る</a>
        </div>
    </body>
    </x-app-layout>
</html>