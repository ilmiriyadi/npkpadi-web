<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Deteksi Nutrisi Padi</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        html { scroll-behavior: smooth; }
        body { font-family: 'Poppins', sans-serif; }

        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #888; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #555; }

        /* FAQ Accordion */
        .faq-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
    </style>
</head>

<body class="relative min-h-screen bg-cover bg-center bg-fixed overflow-x-hidden" 
style="background-image: url('{{ asset('images/sawah.jpg') }}');">

    <!-- Overlay -->

    <div class="relative z-10 flex flex-col min-h-screen">

        <!-- NAVBAR -->
        <nav class="flex items-center justify-between px-8 py-5 max-w-7xl mx-auto w-full">
            <img src="{{ asset('images/greenLogo.png') }}" class="h-14">

            <div class="hidden md:flex bg-green-700 rounded-full p-1 space-x-1">
                <a href="#" class="bg-green-200 text-green-800 px-6 py-2 rounded-full font-medium">Home</a>
                <a href="#about" class="text-white px-6 py-2 rounded-full hover:bg-green-600">About Us</a>
                <a href="#faq" class="text-white px-6 py-2 rounded-full hover:bg-green-600">FAQ</a>
            </div>

            <a href="{{ route('login') }}" 
            class="bg-green-700 text-white px-8 py-2 rounded-full font-bold 
                    hover:bg-green-800 hover:-translate-y-1 hover:shadow-lg 
                    transition duration-300">
                Login
            </a>
        </nav>

        <!-- HERO -->
        <main class="text-center px-4 pt-10 pb-16">
            <h1 class="text-4xl md:text-5xl font-extrabold mb-6">
                Sistem Deteksi Defisiensi Nutrisi<br>Daun Padi Berbasis CNN
            </h1>
            <p class="text-lg md:text-xl max-w-3xl mx-auto">
                Menggunakan Raspberry Pi dan Deep Learning untuk mendeteksi nutrisi tanaman padi secara cepat dan akurat
            </p>
        </main>

        <!-- CARDS -->
        <section class="max-w-7xl mx-auto px-4 pb-20 grid md:grid-cols-3 gap-8">

            <div class="bg-white/60 backdrop-blur-md rounded-3xl p-4 shadow hover:-translate-y-2 transition">
                <img src="{{ asset('images/raspberrypi.jpg') }}" class="rounded-xl mb-4 h-64 w-full object-cover">
                <p class="text-sm text-justify">
                    Raspberry Pi merupakan komputer mini yang digunakan sebagai perangkat utama dalam sistem ini. Raspberry Pi berfungsi untuk menangkap gambar daun melalui kamera dan mengirimkannya ke sistem untuk diproses secara otomatis.
                </p>
            </div>

            <div class="bg-white/60 backdrop-blur-md rounded-3xl p-4 shadow hover:-translate-y-2 transition md:-mt-8">
                <img src="{{ asset('images/padi.jpg') }}" class="rounded-xl mb-4 h-64 w-full object-cover">
                <p class="text-sm text-justify">
                    Padi (Oryza sativa) merupakan komoditas pangan utama yang produktivitasnya sangat dipengaruhi oleh ketersediaan unsur hara makro seperti Nitrogen (N), Fosfor (P), dan Kalium (K). Kekurangan nutrisi tersebut dapat menyebabkan perubahan warna dan bentuk daun yang berdampak pada  kualitas dan hasil panen.
                </p>
            </div>

            <div class="bg-white/60 backdrop-blur-md rounded-3xl p-4 shadow hover:-translate-y-2 transition">
                <img src="{{ asset('images/ai.jpg') }}" class="rounded-xl mb-4 h-64 w-full object-cover">
                <p class="text-sm text-justify">
                    Artificial Intelligence (AI) pada sistem ini diimplementasikan menggunakan metode Convolutional Neural Network (CNN) untuk menganalisis citra daun padi. Model CNN mengklasifikasikan kondisi nutrisi daun, seperti sehat atau mengalami defisiensi N, P, dan K, beserta tingkat akurasi prediksinya.
                </p>
            </div>

        </section>

        <!-- ABOUT US (SIMATA STYLE) -->
        <section id="about" class="bg-white py-24 px-6">
            <div class="max-w-6xl mx-auto flex flex-col md:flex-row items-center gap-12">

                <div class="flex-1 text-center">
                    <img src="{{ asset('images/greenLogo.png') }}" class="w-64 mx-auto">
                </div>

                <div class="flex-1">
                    <h2 class="text-4xl font-extrabold mb-6">
                        About Us <span class="text-green-700"><br>paddy Leaf Nutrition</span>
                    </h2>

                    <p class="text-gray-700 mb-4 text-justify">
                        paddy Leaf Nutrition merupakan sistem berbasis web yang dirancang untuk membantu petani dalam mendeteksi defisiensi nutrisi pada daun padi secara cepat dan akurat. Sistem ini dikembangkan sebagai bagian dari penelitian berjudul “Rancang Bangun Alat Deteksi Defisiensi Nutrisi Daun Padi Menggunakan Raspberry Pi dan Algoritma CNN Berbasis Web.”
                    </p>

                    <p class="text-gray-700 mb-4 text-justify">
                       Sistem ini mengintegrasikan perangkat Raspberry Pi dengan kamera untuk menangkap citra daun padi secara langsung di lapangan. Proses analisis dilakukan menggunakan metode Convolutional Neural Network (CNN) yang mampu mengklasifikasikan kondisi nutrisi daun berdasarkan pola visual yang terdeteksi.
                    </p>

                    <p class="text-gray-700 text-justify">
                        Melalui sistem ini, pengguna tidak hanya memperoleh informasi kondisi kesehatan tanaman (sehat atau defisiensi N, P, dan K), tetapi juga mendapatkan rekomendasi pemupukan yang sesuai. Dengan demikian, sistem diharapkan dapat membantu meningkatkan efisiensi pemupukan, menjaga kesehatan tanaman, dan mendukung peningkatan produktivitas pertanian.
                    </p>
                </div>

            </div>
        </section>

        <!-- FAQ (SIMATA STYLE) -->
       <section id="faq" class="py-24 px-6 bg-gradient-to-br from-[#e8f5e9] via-[#d0f0d4] to-[#c8e6c9]">
            <h2 class="text-center text-4xl font-extrabold mb-16">
                Frequently Asked Questions
            </h2>

            <div class="max-w-3xl mx-auto space-y-4">

                <div class="border rounded-lg bg-white shadow">
                    <button class="faq-btn w-full text-left px-6 py-4 font-semibold">
                        Apa itu paddy Leaf Nutrition?
                    </button>
                    <div class="faq-content px-6">
                        <p class="py-4 text-gray-600">
                            paddy Leaf Nutrition adalah sistem berbasis Artificial Intelligence yang digunakan untuk mendeteksi defisiensi nutrisi pada daun padi menggunakan metode Convolutional Neural Network (CNN) dari citra daun yang diambil menggunakan kamera.
                        </p>
                    </div>
                </div>

                <div class="border rounded-lg bg-white shadow">
                    <button class="faq-btn w-full text-left px-6 py-4 font-semibold">
                        Nutrisi apa yang dideteksi?
                    </button>
                    <div class="faq-content px-6">
                        <p class="py-4 text-gray-600">
                            Sistem dapat mendeteksi beberapa kondisi nutrisi pada tanaman padi, yaitu:<br>
                            Kekurangan Nitrogen (N)<br>
                            Kekurangan Fosfor (P)<br>
                            Kekurangan Kalium (K)
                        </p>
                    </div>
                </div>

                <div class="border rounded-lg bg-white shadow">
                    <button class="faq-btn w-full text-left px-6 py-4 font-semibold">
                        Bagaimana cara kerja sistem paddy Leaf Nutrition?
                    </button>
                    <div class="faq-content px-6">
                        <p class="py-4 text-gray-600">
                            Sistem bekerja dengan cara mengambil gambar daun padi menggunakan kamera yang terhubung dengan Raspberry Pi. Gambar tersebut kemudian diproses oleh model Deep Learning menggunakan metode CNN untuk mengklasifikasikan kondisi nutrisi daun padi.
                        </p>
                    </div>
                </div>

                <div class="border rounded-lg bg-white shadow">
                    <button class="faq-btn w-full text-left px-6 py-4 font-semibold">
                        Apa tujuan dibuatnya sistem ini?
                    </button>
                    <div class="faq-content px-6">
                        <p class="py-4 text-gray-600">
                            Tujuan sistem paddy Leaf Nutrition adalah membantu petani atau pengguna untuk mengetahui kondisi nutrisi tanaman padi lebih cepat dan akurat sehingga dapat dilakukan penanganan atau pemupukan yang tepat guna meningkatkan hasil panen.
                        </p>
                    </div>
                </div>

                <div class="border rounded-lg bg-white shadow">
                    <button class="faq-btn w-full text-left px-6 py-4 font-semibold">
                        Apakah sistem ini dapat digunakan secara real-time?
                    </button>
                    <div class="faq-content px-6">
                        <p class="py-4 text-gray-600">
                            Ya, sistem dirancang untuk dapat digunakan secara real-time menggunakan Raspberry Pi sebagai perangkat pengambil gambar dan sistem klasifikasi berbasis Deep Learning untuk menampilkan hasil deteksi secara langsung.
                        </p>
                    </div>
                </div>

            </div>
        </section>

    </div>

    <!-- FAQ SCRIPT -->
    <script>
        const faqButtons = document.querySelectorAll(".faq-btn");

        faqButtons.forEach(btn => {
            btn.addEventListener("click", () => {
                const content = btn.nextElementSibling;

                if(content.style.maxHeight){
                    content.style.maxHeight = null;
                } else {
                    content.style.maxHeight = content.scrollHeight + "px";
                }
            });
        });
    </script>

    <!-- FOOTER -->
    <footer class="bg-gray-100 mt-auto">

        <!-- Content -->
        <div class="max-w-7xl mx-auto px-6 py-8 grid md:grid-cols-3 gap-6 items-center text-sm">

            <!-- LEFT: Contact -->
            <div class="space-y-2 text-gray-700">
                <p class="flex items-start gap-2">
                    📍 Jl. Brig Jend. Hasan Basri, Pangeran, Kec. Banjarmasin Utara, Kota Banjarmasin, Kalimantan Selatan 70124
                </p>
                <p class="flex items-center gap-2">
                    ✉️ poliban.ac.id
                </p>
                <p class="flex items-center gap-2">
                    📞 (0511) 330 5052
                </p>
            </div>

            <!-- CENTER: Logo -->
            <div class="flex flex-col items-center text-center">
                <img src="{{ asset('images/greenLogo.png') }}" class="h-14 mb-2">
                <p class="text-gray-500 text-xs">
                    © 2026 Politeknik Negeri Banjarmasin - paddy Leaf Nutrition. <br>All Rights Reserved
                </p>
            </div>

            <!-- RIGHT: Social Media -->
            <div class="text-right md:text-right text-center">
                <p class="font-semibold text-gray-700 mb-2">Our Social Media</p>
                <div class="flex md:justify-end justify-center gap-4">
                    <a href="#">
                        <img src="{{ asset('images/instagram.png') }}" 
                            class="w-6 h-6 hover:scale-110 transition">
                    </a>

                    <a href="#">
                        <img src="{{ asset('images/linkedin.png') }}" 
                            class="w-6 h-6 hover:scale-110 transition">
                    </a>

                    <a href="#">
                        <img src="{{ asset('images/facebook.png') }}" 
                            class="w-6 h-6 hover:scale-110 transition">
                    </a>

                    <a href="#">
                        <img src="{{ asset('images/x.png') }}" 
                            class="w-5 h-5 hover:scale-110 transition">
                    </a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>