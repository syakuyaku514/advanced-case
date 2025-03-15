@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')

<div class="detailcard">
    <!-- 店舗詳細 -->
    <div class="detailcard_box">
        <div class="titlebox">
            <button type="button" onClick="history.back()" class="backbtn"><</button>
            <p class="store">{{ $store->store }}</p>
        </div>
        <div class="imagebox">
            @if (strpos($store->image, 'images/') !== false)
                <img src="{{ asset('storage/' . $store->image) }}" alt="{{ $store->store }}" class="detailimg">
            @else
                <img src="{{ asset($store->image) }}" alt="{{ $store->store }}" class="detailimg">
            @endif
            <div class="explanation">
                <div class="storedetail">
                    <p>#{{ $store->region->region }}</p>
                    <p class="genre">#{{ $store->genre->genre }}</p>
                </div>
                <p class="explanacard">{{ $store->overview }}</p> 
            </div> 
        </div>
    </div>

    <!-- 予約カード -->
    <div class="reservationcard">
        <p class="reservationcard_title">予約</p>
        <form action="{{ url('/store/' . $store->id) }}" method="post" class="reservationcard_form">
            @csrf
            <ul class="cardlist">
                <li class="cardlistli">
                    <input name="date" type="date" id="date" min="{{ date('Y-m-d') }}" value="" class="cardlist_form cardlist_input" onchange="updateReservationDetails()" />
                </li>
                <li class="cardlistli">
                    <select name="time" id="time" class="cardlist_form cardlist_select" onchange="updateReservationDetails()">
                        <option value="" selected="">未選択</option>
                        @for($i = 7; $i <= 20; $i++)
                        @for($j = 0; $j <= 5; $j++)
                            <option value="{{$i}}:{{$j}}0">
                                {{$i}}:{{$j}}0
                            </option>
                        @endfor
                        @endfor
                    </select>
                </li>
                <li class="cardlistli">
                    <select name="number" id="peopleInput" class="cardlist_form cardlist_select" onchange="updateReservationDetails()">
                        <option value="" selected>未選択</option>
                        @for($i = 1; $i <= 20; $i++)
                            <option value="{{ $i }}">
                                {{ $i }}人
                            </option>
                        @endfor
                    </select>
                </li>
            </ul>
            <!-- インプットの表示 -->
            <div id="reservationDetails" class="reservationdetail">
                <table class="reservationtable">
                    <tr>
                        <th class="detailtitle">Shop</th>
                        <td class="detailitem">{{ $store->store }}</td>
                    </tr>
                    <tr>
                        <th class="detailtitle">Date</th>
                        <td class="detailitem"><span id="selectedDate" class="detailitem"></span></td>
                    </tr>
                    <tr>
                        <th class="detailtitle">Time</th>
                        <td class="detailitem"><span id="selectedTime" class="detailitem"></span></td>
                    </tr>
                    <tr>
                        <th class="detailtitle">Number</th>
                        <td class="detailitem"><span id="selectedPeople" class="detailitem"></span></td>
                    </tr>
                </table>
            </div>

            <div class="error">
                @if (count($errors) > 0)
                    <p class="errortitle">入力に問題があります</p>  
                @endif

                <div class="error_content">
                    @foreach ($errors->all() as $error)
                        <li class="errortitle">{{$error}}</li>
                    @endforeach
                </div>
            </div>
            <button type="submit" class="reservationbtn">予約する</button>
        </form>
    </div>
</div>

<div class="review_post">
    <a href="{{ route('review.index', ['id' => $store->id]) }}">
        口コミを投稿する
    </a>
</div>

<h2 class="reviewtitle">全ての口コミ情報</h2>
<div>
    @foreach ($storeReviews as $review)
    <!-- 自分の口コミの場合、更新ボタンと削除ボタンを表示 -->
            @if ($review->user_id == Auth::id())
                <!-- 更新ボタン -->
                <div class="review_btn">
                 <a href="javascript:void(0);" class="edit-btn" data-id="edit-form-{{ $review->id }}">口コミを編集</a>

                <!-- 編集フォーム（初期状態は非表示） -->
                <form id="edit-form-{{ $review->id }}" action="{{ route('store_reviews.update', $review->id) }}" method="POST" enctype="multipart/form-data" style="display: none;">
                    @csrf
                    @method('PATCH')
                    <label>評価：</label>
                    <select name="stars">
                        @for ($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ $review->stars == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>

                    <label>コメント：</label>
                    <textarea name="comment">{{ $review->comment }}</textarea>

                    <label>画像</label>
                    <input type="file" name="review_image[]" multiple>

                    <button type="submit">更新</button>
                </form>

                <!-- 削除ボタン -->
                <form id="delete-form-{{ $review->id }}" action="{{ route('store_reviews.destroy', $review->id) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
                </form>

                <a href="javascript:void(0);" class="delete-btn" onclick="event.preventDefault(); if(confirm('本当に削除しますか？')) document.getElementById('delete-form-{{ $review->id }}').submit();">
                    口コミを削除
                </a>
                </div>
                @endif  

        <div class="review">
            <div>
                @for ($i = 1; $i <= 5; $i++)
                    @if ($i <= $review->stars)
                        <!-- 評価された星は青色 -->
                        <img src="{{ asset('img/blue-star.png') }}" alt="評価の星" style="width: 20px; height: 20px;" >
                    @else
                        <!-- 評価されていない星は灰色 -->
                        <img src="{{ asset('img/star-gray.png') }}" alt="無評価の星" style="width: 20px; height: 20px;">
                    @endif
                @endfor
                <p>{{ $review->comment }}</p>
            </div>

            <p>{{ $review->content }}</p>
            
            @if ($review->image) <!-- 画像がある場合 -->
                @php
                    $images = json_decode($review->image);
                @endphp
                <div class="review-images">
                    @foreach ($images as $image)
                        <div class="review-image">
                            <img src="{{ asset('storage/' . $image) }}" alt="レビュー画像" class="reviewimg">
                        </div>
                    @endforeach
                </div>
            @endif
            
        </div>
    @endforeach
</div>


<script>
    // 現在の日付を取得し、最小日付を設定
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('date').setAttribute('min', today);

    // 予約詳細を更新する関数
    function updateReservationDetails() {
        // 各入力要素から値を取得
        const dateInput = document.getElementById('date').value;
        const timeInput = document.getElementById('time').value;
        const peopleInput = document.getElementById('peopleInput').value;

        // 予約内容を表示する要素に値をセット
        document.getElementById('selectedDate').textContent = dateInput || '未選択';
        document.getElementById('selectedTime').textContent = timeInput || '未選択';
        document.getElementById('selectedPeople').textContent = peopleInput ? `${peopleInput}人` : '未選択';
        
        // 時間オプションを更新
        updateTimeOptions(dateInput);
    }

    // 現在以前の時間を選択できないようオプションを更新する関数
    function updateTimeOptions(selectedDate) {
        const timeSelect = document.getElementById('time');
        const now = new Date();
        const currentHour = now.getHours();
        const currentMinute = now.getMinutes();

        // 時間オプションを全て有効化
        for (let i = 0; i < timeSelect.options.length; i++) {
            timeSelect.options[i].disabled = false;
        }

        // 現在の日付が選択された場合、現在時刻より前の時間オプションを無効化
        if (selectedDate === today) {
            for (let i = 0; i < timeSelect.options.length; i++) {
                const optionValue = timeSelect.options[i].value;
                const [hour, minute] = optionValue.split(':').map(Number);

                if (hour < currentHour || (hour === currentHour && minute < currentMinute)) {
                    timeSelect.options[i].disabled = true;
                }
            }
        }
    }

    // ページが読み込まれたときに初期状態を設定
    window.onload = () => {
        updateReservationDetails();
        
        // 入力要素にイベントリスナーを追加
        document.getElementById('date').addEventListener('input', updateReservationDetails);
        document.getElementById('time').addEventListener('input', updateReservationDetails);
        document.getElementById('peopleInput').addEventListener('input', updateReservationDetails);
    };

    // 編集ボタンをクリックした時にフォームを表示
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const formId = this.getAttribute('data-id');
            const form = document.getElementById(formId);
        
            // フォームの表示/非表示を切り替え
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        });
    });
</script>

@endsection