<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Dashboard</title>

    <link rel="stylesheet" href="{{ asset('css/ownerindex.css') }}">
</head>
<body>
    <h1 class="ownertitle">店舗管理者画面です</h1>
    <!-- ログアウトボタン -->
    <form action="{{ route('owner.logout') }}" method="POST">
        @csrf
        <button type="submit" class="ownerlogout">Logout</button>
    </form>
 
    <div class="card">
        <p class="cardtitle">店舗情報の一覧</p>
        <div class="storecard">
            <table >
                <tr>
                    <th>店舗名</th>
                    <th>地域</th>
                    <th>ジャンル</th>
                    <th>店舗説明</th>
                    <th>画像</th>
                </tr>
                @foreach ($stores as $store)
                <tr>
                    <td class="cardstore">{{$store->store}}</td>
                    <td class="cardtag">{{$store->region->region}}</td>
                    <td class="cardtag">{{$store->genre->genre}}</td>
                    <td class="storecard_view">{{$store->overview}}</td>
                    <td>
                        @if (strpos($store->image, 'images/') !== false)
                            <img src="{{ asset($store->image) }}" alt="{{ $store->store }}" class="cardimg">
                        @else
                            <img src="{{ asset($store->image) }}" alt="{{ $store->store }}" class="cardimg">
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('owner.edit', $store->id) }}" method="get">
                            @csrf
                            @method('POST')
                            <button type="submit" class="updatebtn">更新</button>
                        </form>
                    </td>
                    <td>
                        <form action="{{ route('owner.delete',$store->id) }}" method="post">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="editbtn">削除</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
    
    <div class="card">
        <p class="cardtitle">店舗情報の新規作成</p>
        <form action="{{ route('owner.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <table>
                <tr class="cardtr">
                    <th class="cardth">店舗名</th>
                    <th class="cardth">地域</th>
                    <th class="cardth">ジャンル</th>
                    <th class="cardth">店舗説明</th>
                    <th class="cardth">画像</th>
                </tr>
                <tr class="cardtr">
                    <th class="cardth">
                        <input type="text" name="store" id="store" required>
                    </th>
                     <th class="cardth">
                        <select name="region_id" id="region_id" required>
                            <option value="">地域を選択してください</option>
                                @foreach($regions as $region)
                                    <option value="{{$region->id}}">{{$region->region}}</option>
                                @endforeach
                        </select>
                    </th>
                    <th class="cardth">
                        <select name="genre_id" id="genre_id" required>
                            <option value="">ジャンルを選択してください</option>
                                @foreach($genres as $genre)
                                    <option value="{{$genre->id}}">{{$genre->genre}}</option>
                                @endforeach
                        </select>
                    </th>
                    <th class="cardth">
                        <input type="text" name="overview" id="overview" class="storeexplanation" required>
                    </th>
                    <th class="cardth">
                        <input type="file" name="image" id="image" required>
                    </th>
                </tr>
            </table>
            <button type="submit" class="ownerlogout">新規店舗情報作成</button>
        </form>
    </div>

  <div class="card">
    <p class="cardtitle">予約情報の確認</p>
    <table>
        <tr>
           <th>ユーザー名</th>
           <th>予約店舗名</th>
           <th>店舗画像</th>
           <th>日付</th>
           <th>時間</th>
           <th>人数</th>
           <th>来店確認</th>
        </tr>
        @foreach ($reservations as $reservation)
        <tr>
            <td class="cardreservation">{{$reservation->user->name}}</td>
            <td class="cardreservation">{{$reservation->store->store}}</td>
            <td class="cardreservation">
                @if (strpos($reservation->store->image, 'images/') !== false)
                    <img src="{{ asset('storage/' . $reservation->store->image) }}" alt="{{ $reservation->store->store }}" width="100">
                @else
                    <img src="{{ asset($reservation->store->image) }}" alt="{{ $reservation->store->store }}" width="100">
                @endif
            </td>
            <td class="cardreservation">{{$reservation->date}}</td>
            <td class="cardreservation">{{$reservation->time}}</td>
            <td class="cardreservation">{{$reservation->number}}</td>
            <td class="cardreservation">{{$reservation->check}}</td>
        </tr>
        @endforeach
    </table>
  </div>

</body>
</html>