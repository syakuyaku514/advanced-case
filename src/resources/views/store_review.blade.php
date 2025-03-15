@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
<link rel="stylesheet" href="{{ asset('css/done.css') }}">
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="store_review_card">
    <div class="store_review">
    <p class="tanks">今回のご利用はいかがでしたか？</p>
    <div class="card">
        <div class="card__imgframe">
            @if (strpos($store->image, 'images/') !== false)
                <img src="{{ asset($store->image) }}" alt="{{ $store->store }}" class="cardimg">
            @else
                <img src="{{ asset($store->image) }}" alt="{{ $store->store }}" class="cardimg">
            @endif
        </div>
        <div class="card__textbox">
            <div class="card__titletext">
                <p class="storename">{{ $store->store }}</p>
            </div>
            <div class="card__overviewtext">
                <div class="cardtag">
                    <p class="tag">#{{ $store->region->region }}</p>
                    <p class="tag">#{{ $store->genre->genre }}</p>
                </div>
                <div class="cardbtn">
                <form action="{{ route('store.favorite', $store->id) }}" method="post">
                    @csrf
                    <button type="submit" class="hartbtn">
                    @if (Auth::check() && Auth::user()->favorites()->where('store_id', $store->id)->exists())
                        <img src="{{ asset('img/redhart.png')}}" alt="ハート" class="heart">
                    @else
                        <img src="{{ asset('img/grayhart.png')}}" alt="ハート" class="heart">
                    @endif
                    </button>
                </form>
                </div>
            </div>
        </div>
    </div>
    </div>


    <div class="reviewcard">
    <h2 class="reviewtitle">体験を評価してください</h2>
    <form action="{{ route('store_reviews.store') }}" method="post" enctype="multipart/form-data" class="reviewcardform">
        @csrf
        @method('POST')
        <input type="hidden" name="store_id" value="{{ $store->id }}">

        <!-- 評価を隠しフィールドで送信 -->
        <input type="hidden" name="stars" id="star_rating" value="">

        <div class="form-rating">
            <button type="button" class="star-btn" data-value="5">
                <img src="{{ asset('img/star-gray.png') }}" alt="星5" class="star-img">
            </button>
            <button type="button" class="star-btn" data-value="4">
                <img src="{{ asset('img/star-gray.png') }}" alt="星4" class="star-img">
            </button>
            <button type="button" class="star-btn" data-value="3">
                <img src="{{ asset('img/star-gray.png') }}" alt="星3" class="star-img">
            </button>
            <button type="button" class="star-btn" data-value="2">
                <img src="{{ asset('img/star-gray.png') }}" alt="星2" class="star-img">
            </button>
            <button type="button" class="star-btn" data-value="1">
                <img src="{{ asset('img/star-gray.png') }}" alt="星1" class="star-img">
            </button>
        </div>

        <div class="reviewbox">
            <h2>口コミを投稿</h2>
            @if ($errors->any())
            <div class="error-list">
                @foreach ($errors->all() as $error)
                    <p class="error">{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <textarea name="review_content" class="review_textarea" placeholder="カジュアルな夜のお出かけにおすすめのスポット" rows="4">{{ old('review_content') }}</textarea>
            <p class="text">0/400（最高文字数）</p>
          
        </div>

        <div class="imagebox">
        <h2>画像の追加</h2>
            <div class="file-upload-wrapper" id="drag-drop-area">
                <div class="drag-drop-inside">
                    <p class="drag-drop-info">クリックして写真を追加</p>
                    <!-- ファイル選択ボタン -->
                    <input type="file" name="review_image[]" accept="image/*" multiple style="display: none;" id="fileInput">
                    <label for="fileInput" class="file_input">
                        またはドロッグアンドドロップ
                    </label>
                    <div id="image-preview-container" style="margin-top: 15px;"></div>
                </div>
            </div>
        </div>
        <button type="submit" class="submitbtn">口コミを投稿</button>
    </form>
    </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", function() {
    const stars = document.querySelectorAll('.star-btn');
    const ratingInput = document.getElementById('star_rating');

    stars.forEach(star => {
        star.addEventListener('click', function() {
            const value = this.getAttribute('data-value');
            const img = this.querySelector('img');

            if (img.src.indexOf("blue-star.png") === -1) {
                img.src = "{{ asset('img/blue-star.png') }}";
            } else {
                img.src = "{{ asset('img/star-gray.png') }}";
            }

            // 画像サイズを JavaScript で変更する
            img.style.width = "50px"; 
            img.style.height = "50px";

            let selectedStars = 0;
            stars.forEach((star, index) => {
                if (star.querySelector('img').src.indexOf("blue-star.png") !== -1) {
                    selectedStars = index + 1;
                }
            });

            ratingInput.value = selectedStars;
        });
    });
});

document.addEventListener("DOMContentLoaded", function() {
    const fileArea = document.getElementById('drag-drop-area');
    const fileInput = document.getElementById('fileInput');
    const previewContainer = document.getElementById('image-preview-container');

    // ドラッグオーバー時
    fileArea.addEventListener('dragover', function(evt) {
        evt.preventDefault();
        fileArea.classList.add('dragover'); // ドラッグ中のスタイル変更
    });

    // ドラッグ離脱時
    fileArea.addEventListener('dragleave', function(evt) {
        evt.preventDefault();
        fileArea.classList.remove('dragover'); // スタイルを戻す
    });

    // ドロップ時
    fileArea.addEventListener('drop', function(evt) {
        evt.preventDefault();
        fileArea.classList.remove('dragover'); // スタイルを戻す
        const files = evt.dataTransfer.files;
        fileInput.files = files; // 入力フィールドにファイルを設定
        handleFileSelect(); // ファイルを選択してプレビューを表示
    });

    // ファイル選択時にプレビューを表示
    fileInput.addEventListener('change', handleFileSelect);

    // ファイル選択後にプレビューを表示
    function handleFileSelect() {
        const files = fileInput.files;
        previewContainer.innerHTML = ''; // 既存のプレビューをクリア

        Array.from(files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxWidth = '100px';
                img.style.margin = '5px';
                previewContainer.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    }
});
</script>

@endsection
