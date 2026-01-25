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
                @if (!empty($profile?->image_path))
                <img
                    id="avatarPreview"
                    src="{{ asset('storage/' . $profile->image_path) }}"
                    alt="avatar">
                @else
                <img
                    id="avatarPreview"
                    alt="avatar"
                    style="visibility:hidden;">
                @endif
            </div>

            {{-- Cropper.js（CDN） --}}
            <link rel="stylesheet" href="https://unpkg.com/cropperjs@1.6.2/dist/cropper.min.css">
            <script src="https://unpkg.com/cropperjs@1.6.2/dist/cropper.min.js"></script>

            {{-- 画像選択ボタン（選んだ瞬間にトリミング開始） --}}
            <label class="profile-edit__image-button">
                画像を選択する
                <input id="avatarInput" type="file" name="image" accept="image/png,image/jpeg" hidden>
            </label>
        </div>

        @error('image')
        <p class="error-text">{{ $message }}</p>
        @enderror

        <div class="profile-edit__field">
            <label class="profile-edit__label">ユーザー名</label>
            <input class="profile-edit__input" type="text" name="name"
                value="{{ old('name', $profile->display_name ?? $user->name ?? '') }}">
            @error('name')
            <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="profile-edit__field">
            <label class="profile-edit__label">郵便番号</label>
            <input class="profile-edit__input" type="text" name="postal_code"
                value="{{ old('postal_code', $profile->postal_code ?? '') }}">
            @error('postal_code')
            <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="profile-edit__field">
            <label class="profile-edit__label">住所</label>
            <input class="profile-edit__input" type="text" name="address"
                value="{{ old('address', $profile->address ?? '') }}">
            @error('address')
            <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="profile-edit__field">
            <label class="profile-edit__label">建物名</label>
            <input class="profile-edit__input" type="text" name="building_name"
                value="{{ old('building_name', $profile->building_name ?? '') }}">
            @error('building_name')
            <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <button class="profile-edit__submit" type="submit">更新する</button>
    </form>
</div>

{{-- トリミング用モーダル --}}
<div id="cropModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:9999;">
    <div style="width:min(90vw,520px); margin:40px auto; background:#fff; padding:16px; border-radius:10px;">
        <p style="font-weight:700; margin-bottom:10px;">画像を調整</p>

        <div style="width:100%; aspect-ratio: 1 / 1; background:#f3f3f3; overflow:hidden;">
            <img id="cropTarget" style="max-width:100%; display:block;">
        </div>

        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:12px;">
            <button type="button" id="cropCancel">キャンセル</button>
            <button type="button" id="cropOk">この範囲で決定</button>
        </div>
    </div>
</div>

<script>
    (() => {
        const input = document.getElementById('avatarInput');
        const preview = document.getElementById('avatarPreview');
        const modal = document.getElementById('cropModal');
        const cropImg = document.getElementById('cropTarget');
        const btnOk = document.getElementById('cropOk');
        const btnCancel = document.getElementById('cropCancel');

        if (!input || !preview || !modal || !cropImg || !btnOk || !btnCancel) return;

        let cropper = null;

        const openModal = () => {
            modal.style.display = 'block';
        };
        const closeModal = () => {
            modal.style.display = 'none';
        };

        input.addEventListener('change', () => {
            const file = input.files && input.files[0];
            if (!file) return;

            const url = URL.createObjectURL(file);
            cropImg.src = url;

            openModal();

            if (cropper) cropper.destroy();

            cropper = new Cropper(cropImg, {
                aspectRatio: 1,
                viewMode: 1,
                background: false,

                // 重要：枠の外を触っても何も起きない
                dragMode: 'none',
                toggleDragModeOnDblclick: false,

                // 画像は動かさない
                movable: false,

                // 枠は動かせる＆サイズ変更できる（ただしハンドル制御はCSSで）
                cropBoxMovable: true,
                cropBoxResizable: true,

                // 最初から枠を出す（1個だけ）
                autoCropArea: 1,

                // ズームは使える（画像は動かないが拡大縮小はできる）
                zoomable: true,
                zoomOnWheel: true,
                zoomOnTouch: true,

                rotatable: false,
                scalable: false,
            });
        });

        btnCancel.addEventListener('click', () => {
            input.value = '';
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            closeModal();
        });

        btnOk.addEventListener('click', () => {
            if (!cropper) return;

            const canvas = cropper.getCroppedCanvas({
                width: 600,
                height: 600
            });

            canvas.toBlob((blob) => {
                if (!blob) return;

                // プレビュー反映
                preview.src = URL.createObjectURL(blob);
                preview.style.visibility = 'visible';

                // input.files を「切り抜いた画像」に差し替え
                const file = new File([blob], 'profile.jpg', {
                    type: 'image/jpeg'
                });
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;

                cropper.destroy();
                cropper = null;
                closeModal();
            }, 'image/jpeg', 0.9);
        });
    })();
</script>
@endsection