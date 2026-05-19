<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Rice Leaf Nutrition</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { font-family: 'Poppins', sans-serif; }
        svg { display: block; }
    </style>
</head>

<body class="min-h-screen w-full relative bg-white lg:overflow-hidden flex flex-col lg:block">

    <div class="relative lg:absolute lg:top-0 lg:left-0 w-full lg:w-[55%] h-64 lg:h-full shrink-0">
        <img src="{{ asset('images/padi.jpg') }}"
             class="w-full h-full object-cover">

        <div class="absolute inset-0 bg-black/30 lg:bg-black/20"></div>

        <div class="absolute top-8 left-1/2 -translate-x-1/2 lg:translate-x-0 lg:left-8 z-20 flex items-center justify-center">
            <img src="{{ asset('images/whiteLogo.png') }}"
                 class="w-32 md:w-40 drop-shadow-xl">
        </div>
    </div>

    <div class="hidden lg:block absolute top-0 h-full z-10 pointer-events-none"
         style="left: calc(55% - 240px); width: 300px;">
        <svg viewBox="0 0 300 800"
             preserveAspectRatio="none"
             class="h-full w-full">
            <path d="
                M 240,0
                C 240,150  40,220  80,400
                C 120,580  260,640  240,800
                L 300,800
                L 300,0
                Z"
                fill="white"/>
        </svg>
    </div>

    <div class="relative lg:absolute lg:top-0 lg:right-0 w-full lg:w-[45%] flex-grow lg:h-full flex items-center justify-center px-6 py-10 lg:pt-0 z-20 bg-white rounded-t-3xl -mt-6 lg:mt-0 lg:rounded-none shadow-[0_-10px_40px_-15px_rgba(0,0,0,0.1)] lg:shadow-none">

        <div class="w-full max-w-sm">

            <div class="text-center lg:text-left mb-8">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">
                    Selamat Datang
                </h2>
                <p class="text-gray-500 text-sm">Masuk untuk memantau nutrisi lahan Anda.</p>
            </div>

            @if ($errors->any())
                <div class="mb-5 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm font-medium flex items-start shadow-sm">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5 mr-3 text-red-500"></i>
                    <div>
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-5">
                    <label class="block text-xs font-semibold text-gray-600 mb-1 ml-1 uppercase tracking-wider">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" name="email"
                            class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm transition-all"
                            placeholder="email@example.com" required>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-semibold text-gray-600 mb-1 ml-1 uppercase tracking-wider">Password</label>

                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-400"></i>
                        </div>
                        <input id="password" type="password" name="password"
                            class="w-full pl-11 pr-12 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm transition-all"
                            placeholder="••••••••" required>

                        <svg id="eye"
                            class="w-5 h-5 text-gray-400 absolute right-4 top-1/2 -translate-y-1/2 cursor-pointer hover:text-gray-600 transition-colors"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0"/>
                            <path stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5
                                   c4.478 0 8.268 2.943 9.542 7
                                   -1.274 4.057-5.064 7-9.542 7
                                   -4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>

                        <svg id="eyeOff"
                            class="w-5 h-5 text-gray-400 absolute right-4 top-1/2 -translate-y-1/2 cursor-pointer hidden hover:text-gray-600 transition-colors"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2"
                                d="M13.875 18.825A10.05 10.05 0 0112 19
                                   c-4.478 0-8.268-2.943-9.542-7
                                   a9.956 9.956 0 012.042-3.362
                                   m2.122-1.72A9.956 9.956 0 0112 5
                                   c4.478 0 8.268 2.943 9.542 7
                                   a9.97 9.97 0 01-4.043 5.033
                                   M15 12a3 3 0 00-3-3
                                   m0 0a3 3 0 00-3 3
                                   m3-3v3m0 0v3
                                   m9-9l-18 18"/>
                        </svg>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-green-600 text-white font-semibold py-3.5 rounded-xl mt-2 hover:bg-green-700 focus:ring-4 focus:ring-green-500/30 transition duration-200 shadow-lg shadow-green-600/20">
                    Masuk Sekarang
                </button>
            </form>

        </div>
    </div>

    <script>
        const password = document.getElementById("password");
        const eye = document.getElementById("eye");
        const eyeOff = document.getElementById("eyeOff");

        eye.addEventListener("click", () => {
            password.type = "text";
            eye.classList.add("hidden");
            eyeOff.classList.remove("hidden");
        });

        eyeOff.addEventListener("click", () => {
            password.type = "password";
            eyeOff.classList.add("hidden");
            eye.classList.remove("hidden");
        });
    </script>

</body>
</html>