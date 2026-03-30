<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Optimizer Desktop</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 p-10 text-white">
    <div class="bg-slate-800 shadow-2xl mx-auto p-8 border border-slate-700 rounded-xl max-w-xl">
        <h1 class="mb-6 font-bold text-2xl text-center">Media Optimizer</h1>

        @if(session('success'))
            <div class="bg-green-600 mb-4 p-4 rounded">
                {{ session('success') }}
                <a href="{{ asset('storage/' . session('file')) }}" target="_blank" class="block mt-2 text-sm underline">Download Hasil</a>
            </div>
        @endif

        <form action="{{ route('media.process') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-10 border-2 border-slate-600 hover:border-blue-500 border-dashed rounded-lg text-center transition cursor-pointer">
                <input type="file" name="file" class="hidden" id="fileInput" onchange="this.form.submit()">
                <label for="fileInput" class="cursor-pointer">
                    <p class="text-slate-400">Klik atau Drag file gambar/video ke sini</p>
                    <span class="text-slate-500 text-xs">(Maksimal 100MB)</span>
                </label>
            </div>
        </form>
    </div>
</body>
</html>
