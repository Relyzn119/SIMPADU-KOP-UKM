<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileDownloadController extends Controller
{
    /**
     * Serve or download a public storage file securely.
     */
    public function show(Request $request)
    {
        $path = $request->query('path');

        if (!$path) {
            abort(404, 'File tidak ditemukan.');
        }

        // Clean leading slash or storage/ prefix
        $cleanPath = ltrim($path, '/');
        if (str_starts_with($cleanPath, 'storage/')) {
            $cleanPath = substr($cleanPath, 8);
        }

        if (!Storage::disk('public')->exists($cleanPath)) {
            abort(404, 'File tidak ditemukan di sistem penyimpanan.');
        }

        $fullPath = Storage::disk('public')->path($cleanPath);

        if ($request->boolean('download')) {
            return response()->download($fullPath);
        }

        return response()->file($fullPath);
    }
}
