@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endpush

@section('content')
<div class="profile-edit">
    <h1 class="profile-edit__title">プロフィール設定</h1>

    <form class="profile-edit__form" method="POST" action="{{ url('/mypage/profile') }}" enctype="multipart/form-data">
        @csrf

        <div class="profile-edit__top">
            <div class="profile-edit__avatar">
                <img
                    id="avatarPreview"
                    alt="avatar"
                    src="{{ !empty($profile?->image_path) ? asset('storage/' . $profile->image_path) : '' }}"
                    style="{{ !empty($profile?->image_path) ? '' : 'visibility:hidden;' }}">
            </div>

            <label class="profile-edit__image-button">
                画像を選択する
                <input id="avatarInput" type="file" name="image" accept="image/*" hidden>
            </label>
        </div>

        <div class="profile-edit__field">
            <label class="profile-edit__label">ユーザー名</label>
            <input class="profile-edit__input" type="text" name="name"
                value="{{ old('name', $profile->display_name ?? $user->name ?? '') }}">
        </div>

        <div class="profile-edit__field">
            <label class="profile-edit__label">郵便番号</label>
            <input class="profile-edit__input" type="text" name="postal_code"
                value="{{ old('postal_code', $profile->postal_code ?? '') }}">
        </div>

        <div class="profile-edit__field">
            <label class="profile-edit__label">住所</label>
            <input class="profile-edit__input" type="text" name="address"
                value="{{ old('address', $profile->address ?? '') }}">
        </div>

        <div class="profile-edit__field">
            <label class="profile-edit__label">建物名</label>
            <input class="profile-edit__input" type="text" name="building_name"
                value="{{ old('building_name', $profile->building_name ?? '') }}">
        </div>

        <button class="profile-edit__submit" type="submit">更新する</button>
    </form>
</div>

<script>
    const input = document.getElementById('avatarInput');
    const preview = document.getElementById('avatarPreview');

    if (input && preview) {
        input.addEventListener('change', (e) => {
            const file = e.target.files && e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = () => {
                preview.src = reader.result;
                preview.style.visibility = 'visible';
            };
            reader.readAsDataURL(file);
        });
    }
</script>
@endsection