<!-- resources/views/authentication/login.blade.php -->

@extends('.authentication.layouts.main')

@section('container')
    <main class="form-signin w-100 m-auto">
        {{-- Header Gambar dan Text --}}
        <div class="text-center">
            <img class="mb-4 mx-auto" src="/img/samudraLogo.png" alt="Samudra Kue" width="72" height="57">
            <h1 class="h3 mb-3 fw-normal">Login</h1>
        </div>

        {{-- Alert Sukses or Failed --}}
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('loginError'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('loginError') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- form login --}}
        <form action="/login" method="post">
            @csrf
            <div class="form-floating">
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="name@example.com" required>
                <label for="email">Email address</label>
                @error('email')
                    <div id="validationServer04Feedback" class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="form-floating">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                <label for="password">Password</label>
                <span class="position-absolute top-50 end-0 translate-middle-y me-3" onclick="togglePassword()">
                    <i class="fas fa-eye-slash" id="togglePasswordIcon"></i>
                </span>
            </div>

            <button class="btn btn-primary w-100 py-2" type="submit">Sign in</button>
            <p class="mt-5 mb-3 text-body-secondary text-center">&copy; Samud Kue 2023 - 2024</p>
        </form>
    </main>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const togglePasswordIcon = document.getElementById('togglePasswordIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                togglePasswordIcon.classList.remove('fa-eye-slash');
                togglePasswordIcon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                togglePasswordIcon.classList.remove('fa-eye');
                togglePasswordIcon.classList.add('fa-eye-slash');
            }
        }
    </script>
@endsection
