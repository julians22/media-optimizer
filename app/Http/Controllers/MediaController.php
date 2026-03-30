<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use FFMpeg\Format\Video\X264;
use Native\Laravel\Dialog;


class MediaController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function process(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:102400', // Limit 100MB
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $mime = $file->getMimeType();

        if (str_contains($mime, 'image')) {
            return $this->handleImage($file);
        } elseif (str_contains($mime, 'video')) {
            return $this->handleVideo($file);
        }

        return back()->with('error', 'Format file tidak didukung.');
    }

    private function handleImage($file)
    {
        $name = time() . '.webp'; // Kita paksa ke format WebP untuk kompresi terbaik
        $path = storage_path('app/public/' . $name);

        // Menggunakan GD untuk convert ke WebP (sudah kita install di Docker)
        $image = imagecreatefromstring(file_get_contents($file));
        imagewebp($image, $path, 80); // Quality 80
        imagedestroy($image);

        // Jalankan Image Optimizer (Lossless)
        ImageOptimizer::optimize($path);

        return back()->with('success', 'Gambar berhasil dikompres ke WebP!')->with('file', $name);
    }

    private function handleVideo($file)
    {
        $inputPath = $file->store('temp', 'local');
        $outputName = time() . '.mp4';

        // Konversi Video menggunakan FFmpeg (X264)
        FFMpeg::fromDisk('local')
            ->open($inputPath)
            ->export()
            ->toDisk('public')
            ->inFormat(new X264('libmp3lame', 'libx264'))
            ->save($outputName);

        Storage::disk('local')->delete($inputPath);

        return back()->with('success', 'Video berhasil dikonversi ke MP4!')->with('file', $outputName);
    }


    public function openFile()
    {
        // Ini akan memanggil jendela "Open File" asli Windows
        $file = Dialog::new()
            ->filter('Images', ['jpg', 'png', 'gif'])
            ->filter('Videos', ['mp4', 'avi', 'mkv'])
            ->title('Pilih Gambar atau Video dari Windows')
            ->open();

        if ($file) {
            // Karena file dari Windows diakses lewat /mnt/c/ di WSL
            // Kita perlu memproses path ini
            return $this->processExternalFile($file);
        }
    }
}
