<!DOCTYPE html>
<script src="https://kit.fontawesome.com/4b4f952461.js" crossorigin="anonymous"></script>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <x-app-layout>
    <x-slot name="header">
    index
    </x-slot>
    
    <div class="container">
    	<div class="row">
            <div class="col-md-6">
        		<h2>Custom search field</h2>
                <div id="custom-search-input">
                    <div class="input-group col-md-12">
                        <form action="{{ route('posts.search') }}" method="GET">
                            {{ csrf_field() }}
                        <input type="text" class="form-control input-lg" placeholder="Buscar" name="search">
                        <span class="input-group-btn">
                            <button class="btn btn-info btn-lg" type="submit">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
    	</div>
    </div>
    
    @isset($search_result)
    <h5 class="card-title">{{ search_result }}</h5>
    @endisset
    
    <body class="antianliased">
        <h1>投稿一覧</h1>
        <a href='/posts/create'>create</a>
        <div class='posts'>
            @foreach ($posts as $post)
                <div class='post'>
                    <img src="{{ $post->image_path }}" alt="画像が読み込めません。" width="600" height="400">
                </div>
                    <a href="/posts/{{ $post->id }}"><h2 class='title'>{{ $post->title }}</h2></a>
                    <p class='body'>{{ $post->body }}</p>
                    <form action="/posts/{{ $post->id }}" id="form_{{ $post->id }}" method="post">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="deletePost({{ $post->id }})">delete</button> 
                    </form>
                </div>
            @endforeach
        </div>
        <h1>Other Users</h1>
            <ul> 
                @foreach($users as $user) 
                    <li>{{ $user->name }} - {{ $user->email }}</li>
                @endforeach 
            </ul>
        <script>
            function deletePost(id) {
                'use strict'
        
                if (confirm('削除すると復元できません。\n本当に削除しますか？')) {
                    document.getElementById(`form_${id}`).submit();
                }
            }
        </script>
        <h1>ログインユーザー:{{ Auth::user()->name }}</h1>
        </body>
    </x-app-layout>
</html>
