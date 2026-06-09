<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Serves files from the public storage disk via authenticated routes.
 * 
 * This avoids relying on the public/storage symlink, which may not work
 * on all hosting environments (shared hosting, certain server configs, etc.)
 * causing 403 Forbidden errors.
 */
class FileController extends Controller
{
    /**
     * Serve a file from the public storage disk.
     *
     * @param string $path The relative path within the public disk
     * @return StreamedResponse|\Illuminate\Http\JsonResponse
     */
    public function serve(string $path)
    {
        // Sanitize: prevent directory traversal
        $path = str_replace('..', '', $path);

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File not found.');
        }

        $mimeType = Storage::disk('public')->mimeType($path) ?? 'application/octet-stream';

        return response()->stream(function () use ($path) {
            $stream = Storage::disk('public')->readStream($path);
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
