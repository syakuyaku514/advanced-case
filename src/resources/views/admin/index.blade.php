<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者画面</title>
    <link rel="stylesheet" href="{{ asset('css/admin/admin.index.css') }}">
</head>
<body>
    <h1>管理者画面</h1>

    <div class="adminform">
        <form action="{{ route('admin.logout') }}" method="POST">
        @csrf
            <button type="submit" class="indexbtn">Logout</button>
        </form>
        <a href="{{ route('admin.sendEmailForm') }}">
            <button class="indexbtn">メール送信画面へ</button>
        </a>
    </div>

    <div class="adminindex">
    <div class="admincreate">
        <p class="admincreatep">店舗代表者作成</p>

        <form method="POST" action="{{ route('admin.owner.register.submit') }}">
            @csrf
            <div>
                <label for="name" class="formlabel">名前</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required>
            </div>
            <div>
                <label for="email" class="formlabel">メールアドレス</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required>
            </div>
            <div>
                <label for="password" class="formlabel">パスワード</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div>
                <label for="password_confirmation" class="formlabel">パスワード確認</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required>
            </div>
            <div>
                <button type="submit" class="createbtn">新規作成</button>
            </div>
        </form>

        {{-- 成功メッセージの表示 --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- エラーメッセージの表示 --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>


        <div class="adminview">
            <p class="admincreatep">店舗代表者一覧</p>
            @foreach ($owners as $owner)
            <table>
                <tr>
                   <td class="adminviewname">{{$owner->name}}</td>
                   <td class="adminviewemail">{{$owner->email}}</td>
                </tr>
            </table>
            @endforeach
        </div>
    </div>


    
<!-- CSVインポート -->
<div class="container mt-5">
    <h2>CSVインポート</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('csvImport') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group mb-4">
            <div class="custom-file text-left">
                <input type="file" name="csvFile" class="custom-file-input" id="csvFile" required>
            </div>
        </div>
        <button class="btn btn-primary btn-lg">インポート</button>
    </form>
</div>





@foreach ($stores as $store)
    @if ($store->storeReviews->isNotEmpty())
        <h3>{{ $store->name }}</h3>
        <table border="1">
            <tr>
                <th>ユーザー</th>
                <th>店舗名</th>
                <th>コメント</th>
                <th>評価</th>
                <th>画像</th>
                <th>削除</th>
            </tr>
            @foreach ($store->storeReviews as $review)
                <tr>
                    <td>{{ $review->user->name }}</td>
                    <td>{{ $review->store->store }}</td>
                    <td>{{ $review->comment }}</td>
                    <td>
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= $review->stars)
                            <!-- 評価された星は青色 -->
                            <img src="{{ asset('img/blue-star.png') }}" alt="評価の星" style="width: 20px; height: 20px;" class="star">
                        @else
                            <!-- 評価されていない星は灰色 -->
                            <img src="{{ asset('img/star-gray.png') }}" alt="無評価の星" style="width: 20px; height: 20px;">
                        @endif
                        @endfor
                    </td>
                    <td>
                        @if ($review->image)
                        @php
                            $images = json_decode($review->image);
                        @endphp
                            <div class="review-images">
                            @foreach ($images as $image)
                            <div class="review-image">
                                <img src="{{ asset('storage/' . $image) }}" alt="レビュー画像" style="max-width: 100%; height: auto;">
                            </div>
                            @endforeach
                           </div>
                        @endif
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.deleteReview', $review->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit">削除</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    @endif
@endforeach



</body>
</html>