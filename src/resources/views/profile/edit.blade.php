@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
<link rel="stylesheet" href="https://unpkg.com/cropperjs@1.6.2/dist/cropper.min.css">
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
                    class="profile-edit__avatar-img"
                    src="{{ Storage::url($profile->image_path) }}"
                    alt="avatar">
                @else
                <img
                    id="avatarPreview"
                    class="profile-edit__avatar-img profile-edit__avatar-img--hidden"
                    alt="avatar">
                @endif
            </div>

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

<div id="cropModal" class="crop-modal" aria-hidden="true">
    <div class="crop-modal__panel" role="dialog" aria-modal="true" aria-label="画像を調整">
        <p class="crop-modal__title">画像を調整</p>

        <div class="crop-modal__stage">
            <img id="cropTarget" class="crop-modal__img" alt="crop target">
        </div>

        <div class="crop-modal__actions">
            <button type="button" id="cropCancel" class="crop-modal__btn crop-modal__btn--ghost">キャンセル</button>
            <button type="button" id="cropOk" class="crop-modal__btn crop-modal__btn--primary">この範囲で決定</button>
        </div>
    </div>
</div>

<script src="https://unpkg.com/cropperjs@1.6.2/dist/cropper.min.js"></script>

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
            modal.setAttribute('aria-hidden', 'false');
        };

        const closeModal = () => {
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
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

                dragMode: 'none',
                toggleDragModeOnDblclick: false,

                movable: false,

                cropBoxMovable: true,
                cropBoxResizable: true,

                autoCropArea: 1,

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

                preview.src = URL.createObjectURL(blob);
                preview.classList.remove('profile-edit__avatar-img--hidden');

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