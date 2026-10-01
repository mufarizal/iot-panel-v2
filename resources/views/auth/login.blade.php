<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - CMSStaging Monitoring</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-sm">
        <h1 class="text-xl font-semibold mb-6 text-center">CMSStaging Monitoring</h1>

        @if ($errors->any())
        <div class="mb-4 text-sm text-red-600">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white rounded py-2 text-sm font-medium hover:bg-blue-700">
                Login
            </button>
        </form>
    </div>

</body>
</html>
