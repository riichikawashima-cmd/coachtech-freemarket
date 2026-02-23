<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @stack('styles')
</head>

<body>
    <header class="header">
        @php
        $logoHref = url('/');

        if (auth()->check()) {
        if (! auth()->user()->hasVerifiedEmail()) {
        $logoHref = route('verification.notice');
        } else {
        $profile = \App\Models\Profile::where('user_id', auth()->id())->first();

        $needsProfile = empty($profile)
        || empty($profile->postal_code)
        || empty($profile->address);

        if ($needsProfile) {
        $logoHref = url('/mypage/profile');
        }
        }
        }
        @endphp

        <a href="{{ $logoHref }}">
            <img src="{{ asset('images/coachtech-logo.png') }}" alt="COACHTECH">
        </a>

        @unless (
        request()->routeIs('login') ||
        request()->routeIs('register') ||
        request()->routeIs('verification.notice')
        )
        <form method="GET" action="{{ route('items.index') }}" class="header-search">
            <input
                type="text"
                name="keyword"
                value="{{ request('keyword') }}"
                placeholder="なにをお探しですか？">
            <input type="hidden" name="tab" value="{{ request('tab', 'recommend') }}">
        </form>

        <div class="header-actions">
            @auth
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit">ログアウト</button>
            </form>
            <a href="/mypage">マイページ</a>
            <a href="/sell" class="header-sell">出品</a>
            @endauth

            @guest
            <a href="{{ route('login') }}">ログイン</a>
            <a href="{{ route('login') }}">マイページ</a>
            <a href="{{ route('register') }}">会員登録</a>
            @endguest
        </div>
        @endunless
    </header>

    <main>
        @yield('content')
    </main>

    <script>
        (() => {
            if (location.pathname.startsWith('/sell')) return;

            const KEY = 'scrollY';

            const y = sessionStorage.getItem(KEY);
            if (y !== null) {
                window.scrollTo(0, parseInt(y, 10));
                sessionStorage.removeItem(KEY);
            }

            document.addEventListener('submit', () => {
                sessionStorage.setItem(KEY, String(window.scrollY));
            }, true);

            document.addEventListener('click', (e) => {
                const a = e.target.closest('a');
                if (a && a.href && !a.target) {
                    sessionStorage.setItem(KEY, String(window.scrollY));
                }
            }, true);
        })();

        const imgInput = document.getElementById('sellImageInput');
        const imgPreview = document.getElementById('sellImagePreview');

        if (imgInput && imgPreview) {
            imgInput.addEventListener('change', () => {
                const file = imgInput.files && imgInput.files[0];

                if (!file) {
                    imgPreview.src = '';
                    imgPreview.style.display = 'none';
                    return;
                }

                if (!file.type.startsWith('image/')) {
                    imgInput.value = '';
                    imgPreview.src = '';
                    imgPreview.style.display = 'none';
                    return;
                }

                const url = URL.createObjectURL(file);
                imgPreview.src = url;
                imgPreview.style.display = 'block';
            });
        }

        document.addEventListener('click', (e) => {
            document.querySelectorAll('.cselect.is-open').forEach(el => {
                if (!el.contains(e.target)) {
                    el.classList.remove('is-open');
                    const b = el.querySelector('.cselect__button');
                    if (b) b.setAttribute('aria-expanded', 'false');
                }
            });

            const box = e.target.closest('.cselect');
            if (!box) return;

            const btn = e.target.closest('.cselect__button');
            if (btn) {
                box.classList.toggle('is-open');
                btn.setAttribute('aria-expanded', box.classList.contains('is-open') ? 'true' : 'false');
                return;
            }

            const opt = e.target.closest('.cselect__option');
            if (opt) {
                box.querySelectorAll('.cselect__option').forEach(o => o.setAttribute('aria-selected', 'false'));
                opt.setAttribute('aria-selected', 'true');

                const value = opt.dataset.value ?? '';
                const textEl = opt.querySelector('.cselect__option-text');
                const text = textEl ? textEl.textContent.trim() : opt.textContent.trim();

                box.querySelector('input[type="hidden"]').value = value;
                box.querySelector('.cselect__text').textContent = text;

                const summary = document.getElementById('summaryPaymentMethodRight');
                if (summary) summary.textContent = text;

                fetch("{{ route('purchase.payment_method') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json",
                    },
                    body: JSON.stringify({
                        payment_method: value,
                        label: text
                    }),
                });

                box.classList.remove('is-open');
                box.querySelector('.cselect__button').setAttribute('aria-expanded', 'false');
            }
        });
    </script>
</body>

</html>