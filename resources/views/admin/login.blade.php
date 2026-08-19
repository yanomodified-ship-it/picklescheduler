<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | HomeCourt PickleHouse</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-6 antialiased">
    <div class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-3xl p-8 shadow-2xl">
        <div class="text-center mb-8">
            <span class="text-4xl">🏓</span>
            <h1 class="text-2xl font-extrabold text-lime-400 mt-2">HomeCourt Admin</h1>
            <p class="text-xs text-slate-400 uppercase tracking-widest mt-1">Management Portal Login</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-500/30 bg-red-900/20 p-4 text-xs text-red-300">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-5">
            @csrf
            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full rounded-xl border border-slate-700 bg-slate-800 px-4 py-3 text-white outline-none focus:border-lime-400">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Password</label>
                <input id="password" type="password" name="password" required class="w-full rounded-xl border border-slate-700 bg-slate-800 px-4 py-3 text-white outline-none focus:border-lime-400">
            </div>

            <div class="flex items-center justify-between text-xs text-slate-400">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-slate-700 bg-slate-800 text-lime-400 focus:ring-0">
                    <span>Remember me</span>
                </label>
            </div>

            <button type="submit" class="w-full rounded-xl bg-lime-400 py-3.5 font-bold text-slate-950 hover:bg-lime-300 transition shadow-lg shadow-lime-400/20">
                Log In to Dashboard
            </button>
        </form>
    </div>
</body>
</html>