@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
<link rel="stylesheet" href="{{ asset('css/done.css') }}">
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="store_review_card">
    <div class="store_review_update">
    <p class="thanks">今回のご利用はいかがでしたか？</p>
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

    <label>現在の画像：</label>
            @if (!empty($review->image))
                @foreach (json_decode($review->image, true) as $index => $imagePath)
                    <div class="existing-image">
                        <img src="{{ asset('storage/' . $imagePath) }}" alt="口コミ画像" width="100" class="current-image" data-index="{{ $index }}">
                        <input type="file" name="review_image[]" class="image-input" style="display: none;" data-index="{{ $index }}">
                        <label>
                            <input type="checkbox" name="delete_images[]" value="{{ $imagePath }}"> 削除
                        </label>
                    </div>
                @endforeach
            @else
                <p>画像はありません</p>
            @endif

    </div>


    <div class="reviewcard">
    <h2 class="reviewtitle">
        評価を編集
    </h2>
    <form action="{{ route('store_reviews.update', ['id' => $review->id]) }}" method="POST" enctype="multipart/form-data" class="reviewcardform">
        @csrf
        @method('PATCH')

        <input type="hidden" name="store_id" value="{{ $store->id }}">
        {{-- ★ 修正：現在の評価をデフォルトでセット --}}
        <input type="hidden" name="stars" id="star_rating" value="{{ old('stars', $review->stars) }}">

        <div class="form-rating">
            @for ($i = 1; $i <= 5; $i++)
                <button type="button" class="star-btn" data-value="{{ $i }}">
                    <img src="{{ asset('img/' . ($i <= old('stars', $review->stars) ? 'blue-star.png' : 'star-gray.png')) }}" 
                         alt="星{{ $i }}" class="star-img">
                </button>
            @endfor
        </div>

        <div class="reviewbox">
            <h2>口コミを編集</h2>

            @if ($errors->any())
                <div class="error-list">
                    @foreach ($errors->all() as $error)
                        <p class="error">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <textarea name="comment" class="review_textarea" rows="4">{{ old('comment', $review->comment) }}</textarea>
            <p class="text">0/400（最高文字数）</p>         
        </div>

        <div class="imagebox">
            <h2>画像の追加</h2>
            <div class="file-upload-wrapper" id="drag-drop-area">
                <div class="drag-drop-inside">
                    <p class="drag-drop-info">クリックして新しい写真を追加</p>
                    <input type="file" name="review_image[]" accept="image/*" multiple id="new-images" onchange="previewImages()" style="display: none;">
                    <label for="new-images" class="file_input">またはドラッグアンドドロップ</label>
                    <div id="new-images-preview" style="margin-top: 15px;"></div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <button type="submit" class="updatebtn">口コミを更新</button>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const fileArea = document.getElementById('drag-drop-area');
    const fileInput = document.getElementById('new-images');
    const previewContainer = document.getElementById('new-images-preview');

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
        previewImages(); // ファイルを選択してプレビューを表示
    });

    // ファイル選択時にプレビューを表示
    fileInput.addEventListener('change', previewImages);

    // プレビューの表示処理
    function previewImages() {
        previewContainer.innerHTML = ''; // 既存のプレビューをクリア
        const files = fileInput.files;

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

// 星評価の処理
document.querySelectorAll('.star-btn').forEach(button => {
    button.addEventListener('click', function() {
        // クリックされたボタンの値を取得
        const rating = this.getAttribute('data-value');

        // 評価をhiddenフィールドに設定
        document.getElementById('star_rating').value = rating;

        // すべての星ボタンから選択を解除
        document.querySelectorAll('.star-btn').forEach(btn => {
            btn.classList.remove('selected');
        });

        // クリックされた星ボタンを選択状態にする
        this.classList.add('selected');

        // すべての星の画像を更新
        document.querySelectorAll('.star-btn img').forEach((img, index) => {
            if (index < rating) {
                img.src = '{{ asset("img/blue-star.png") }}'; // 選択された星を青色に
            } else {
                img.src = '{{ asset("img/star-gray.png") }}'; // 選択されていない星をグレーに
            }
        });
    });
});
</script>

@endsection
