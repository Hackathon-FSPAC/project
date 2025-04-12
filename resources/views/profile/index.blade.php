@extends('layouts.app')
@extends('layouts.panel')

@section('content')
<div class="max-w-2xl mx-auto py-10 px-4">

    <h2 class="text-2xl font-bold mb-6">👤 Profilul tău</h2>

    <div class="bg-white shadow p-5 rounded-xl mb-6 text-center">
        {{-- Poză de profil --}}
        <img src="{{ $user->profile_photo ? asset('storage/'.$user->profile_photo) : 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}"
             class="w-24 h-24 rounded-full mx-auto mb-4">

        <h3 class="text-xl font-semibold">{{ $user->name }}</h3>
        <p class="text-sm text-gray-500">{{ $user->email }}</p>

        {{-- Form pentru schimbarea pozei --}}
        <form method="POST" action="{{ route('profile.photo') }}" enctype="multipart/form-data" class="mt-4">
            @csrf
            <input type="file" name="profile_photo" class="mb-2">
            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Schimbă poza
            </button>
        </form>
    </div>

    <h3 class="text-lg font-semibold mb-4">📝 Postările tale</h3>

    @foreach($posts as $item)
    <div class="bg-white shadow rounded-xl p-5 mb-4">
        <p class="text-gray-800">{{ $item->content }}</p>

        @if($item->image_path)
            <img src="{{ asset('storage/' . $item->image_path) }}" class="mt-3 rounded-xl max-h-64 object-cover">
        @endif

        <div class="text-sm text-gray-500 mt-2 flex justify-between items-center">
            <span>{{ $item->created_at->diffForHumans() }}</span>

            <form method="POST" action="{{route('feed.delete', $item) }}">
                @csrf
                @method('DELETE')
                <button class="text-red-500 hover:underline">🗑️ Șterge</button>
            </form>
        </div>
    </div>
    @endforeach

</div>
@endsection
