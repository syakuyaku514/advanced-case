@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
<link rel="stylesheet" href="{{ asset('css/done.css') }}">
@endsection

@section('content')
<div class="card thankscard">
    <p class="tanks thanks_text">ご予約ありがとうございます</p>
    <a class="thanksbackbtn" href="/mypage">戻る</a>
</div>
@endsection