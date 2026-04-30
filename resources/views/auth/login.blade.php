<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { max-width: 420px; margin: 80px auto; }
    </style>
</head>
<body>

<div class="card shadow-sm">
    <div class="card-body">
        <h3 class="card-title mb-3 text-center">Sign in</h3>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <!-- allow demo buttons to force which database to try first -->
            <input type="hidden" name="force_mode" id="force_mode" value="">

            <div class="mb-3">
                <label for="login" class="form-label">Username or Email</label>
                <input id="login" name="login" type="text" value="{{ old('login') }}" required autofocus class="form-control">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input id="password" name="password" type="password" required class="form-control">
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>
    </div>
</div>
</div>

<!-- Demo sessions box (below login) -->
<div class="card shadow-sm mt-3" style="max-width:420px;margin:0 auto 40px;">
    <div class="card-body">
        <h5 class="card-title mb-3">Click to Start Session!</h5>

        <div class="table-responsive">
            <table class="table table-borderless align-middle mb-2">
                <tbody>
                <tr>
                    <td>admin</td>
                    <td class="text-muted">123456</td>
                    <td class="text-end"><button class="btn btn-info text-white apply-demo" data-username="admin">Apply</button></td>
                </tr>
                <tr>
                    <td>inventory</td>
                    <td class="text-muted">123456</td>
                    <td class="text-end"><button class="btn btn-info text-white apply-demo" data-username="inventory">Apply</button></td>
                </tr>
                <tr>
                    <td>sales</td>
                    <td class="text-muted">123456</td>
                    <td class="text-end"><button class="btn btn-info text-white apply-demo" data-username="sales">Apply</button></td>
                </tr>
                </tbody>
            </table>
        </div>

        <p class="mb-0"><span class="text-secondary">&#9432;</span> <small class="text-muted">Some of the features are disabled in demo and it will be reset after each hour.</small></p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.apply-demo').forEach(function(btn){
            btn.addEventListener('click', function(e){
                var username = e.currentTarget.getAttribute('data-username');
                var loginInput = document.getElementById('login');
                var passwordInput = document.getElementById('password');
                if(loginInput) loginInput.value = username;
                if(passwordInput) passwordInput.value = '123456';
                // set the force_mode to demo so server tries demo DB first
                var force = document.getElementById('force_mode');
                if(force) force.value = 'demo';
                // Submit the form to sign in immediately
                var form = document.querySelector('form');
                if(form) form.submit();
            });
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
