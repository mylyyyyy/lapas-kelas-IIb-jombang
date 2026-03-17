<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">commit
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terjadi Kendala Teknis - Lapas Kelas IIB Jombang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-gradient-mesh {
            background-color: #0f172a;
            background-image: 
                radial-gradient(at 0% 100%, hsla(0,100%,15%,0.3) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(217,100%,10%,1) 0, transparent 50%);
        }
    </style>
</head>
<body class="bg-gradient-mesh text-white min-h-screen flex items-center justify-center p-6 overflow-hidden">
    <div class="max-w-2xl w-full text-center relative z-10">
        <div class="mb-10">
            <div class="relative inline-block">
                <div class="absolute inset-0 bg-red-500/20 blur-2xl rounded-full animate-pulse"></div>
                <div class="relative bg-gradient-to-br from-red-500 to-rose-700 w-32 h-32 rounded-3xl flex items-center justify-center shadow-2xl">
                    <i class="fas fa-exclamation-triangle text-5xl text-white"></i>
                </div>
            </div>
        </div>

        <h1 class="text-4xl md:text-5xl font-extrabold mb-6 tracking-tight">
            Ups! Terjadi <br>
            <span class="bg-gradient-to-r from-red-400 to-rose-500 bg-clip-text text-transparent">Kendala Teknis</span>
        </h1>
        
        <p class="text-blue-200/70 text-lg md:text-xl leading-relaxed mb-10 max-w-lg mx-auto">
            Maaf, sistem mengalami kendala teknis sementara. Tim IT kami telah menerima notifikasi dan sedang memperbaikinya.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <a href="/" class="group flex items-center gap-3 px-8 py-4 bg-white/10 border border-white/20 rounded-2xl hover:bg-white/20 transition-all active:scale-95">
                <i class="fas fa-home text-blue-400"></i>
                <span class="font-bold">Kembali ke Beranda</span>
            </a>
            <button onclick="window.location.reload()" class="group flex items-center gap-3 px-8 py-4 bg-white text-slate-900 rounded-2xl transition-all shadow-lg active:scale-95">
                <i class="fas fa-redo"></i>
                <span class="font-bold">Coba Lagi</span>
            </button>
        </div>

        <div class="mt-20 pt-8 border-t border-white/5 opacity-30">
            <p class="text-[10px] font-mono tracking-widest text-blue-200 uppercase">
                Error Code: 500 | Internal Server Error
            </p>
        </div>
    </div>
</body>
</html>
