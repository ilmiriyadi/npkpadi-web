<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Rice Leaf Nutrition</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-[#56A059] min-h-screen flex items-center justify-center p-4">

    <div class="flex flex-col md:flex-row bg-white rounded-[2rem] shadow-2xl overflow-hidden max-w-4xl w-full">
        
        <div class="md:w-1/2 bg-cover bg-center relative min-h-[400px]" style="background-image: url('https://images.unsplash.com/photo-1590682680695-43b964a3ae17?q=80&w=1000&auto=format&fit=crop');">
            <div class="absolute inset-0 bg-black/30 flex items-center justify-center p-8">
                <div class="text-white text-5xl font-bold flex flex-col items-center text-center leading-tight">
                    <div class="flex items-center mb-2">
                        <svg class="w-12 h-12 mr-3 text-yellow-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>
                        <span>Rice Leaf</span>
                    </div>
                    <span>Nutrition</span>
                </div>
            </div>
        </div>

        <div class="md:w-1/2 p-10 md:p-14 flex flex-col justify-center">
            <h2 class="text-3xl font-bold text-[#387F39] mb-8 text-center">Silahkan Login</h2>
            
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#387F39] focus:outline-none" placeholder="Enter your email" value="{{ old('email') }}" required autofocus>
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <input type="password" name="password" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#387F39] focus:outline-none" placeholder="Enter your password" required>
                        <svg class="w-5 h-5 text-gray-400 absolute right-4 top-3.5 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm" />
                </div>

                <div class="flex items-center justify-between mb-8">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-[#387F39] focus:ring-[#387F39] w-4 h-4">
                        <span class="ml-2 text-sm text-gray-700 font-medium">Remember me</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-medium text-gray-700 hover:text-[#387F39]">Forgot Password</a>
                    @endif
                </div>

                <button type="submit" class="w-full bg-[#387F39] hover:bg-green-800 text-white font-bold py-3 px-4 rounded-xl transition-colors shadow-lg shadow-green-200">
                    Sign in
                </button>
            </form>

            <div class="my-6 flex items-center justify-center">
                <span class="text-sm text-gray-500 font-medium">Or</span>
            </div>

            <button type="button" class="w-full bg-white border border-gray-300 text-gray-700 font-semibold py-3 px-4 rounded-xl flex items-center justify-center hover:bg-gray-50 transition-colors shadow-sm">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5 mr-3" alt="Google">
                Sign in with Google
            </button>
        </div>
    </div>

</body>
</html>