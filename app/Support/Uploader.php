<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Profile photo handling.
 *
 * The original stored uploads under the user's own filename, so two people
 * uploading "photo.jpg" overwrote each other and both profile rows pointed at
 * the survivor. Names here are random and the extension is derived from the
 * detected image type, never from the client-supplied name.
 */
final class Uploader
{
    /**
     * @param  array{name:string,type:string,tmp_name:string,error:int,size:int} $file
     * @return array{ok:bool, filename:?string, message:string}
     */
    public static function storeImage(array $file): array
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return self::fail(match ($file['error']) {
                UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'That image is too large.',
                UPLOAD_ERR_PARTIAL                        => 'The upload did not finish. Try again.',
                default                                   => 'The image could not be uploaded.',
            });
        }

        $maxBytes = (int) config('uploads.max_bytes', 4 * 1024 * 1024);

        if ($file['size'] > $maxBytes) {
            return self::fail('Images must be ' . round($maxBytes / 1048576, 1) . ' MB or smaller.');
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            return self::fail('The image could not be verified.');
        }

        // Trust the file's actual contents, not its name or the browser's
        // Content-Type header.
        $info = @getimagesize($file['tmp_name']);

        if ($info === false) {
            return self::fail('That file is not an image.');
        }

        $extensions = [
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG  => 'png',
            IMAGETYPE_WEBP => 'webp',
        ];

        $type = $info[2] ?? null;

        if (!isset($extensions[$type])) {
            return self::fail('Photos must be JPEG, PNG or WebP.');
        }

        $directory = self::directory();

        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            return self::fail('The uploads folder is not writable.');
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $extensions[$type];

        if (!move_uploaded_file($file['tmp_name'], $directory . '/' . $filename)) {
            return self::fail('The image could not be saved.');
        }

        return ['ok' => true, 'filename' => $filename, 'message' => ''];
    }

    public static function delete(?string $filename): void
    {
        if (!is_string($filename) || $filename === '') {
            return;
        }

        // basename() keeps a crafted value like "../../config/config.php" from
        // escaping the uploads directory.
        $path = self::directory() . '/' . basename($filename);

        if (is_file($path)) {
            @unlink($path);
        }
    }

    private static function directory(): string
    {
        return BASE_PATH . '/' . trim((string) config('uploads.dir', 'public/uploads'), '/');
    }

    /** @return array{ok:bool, filename:?string, message:string} */
    private static function fail(string $message): array
    {
        return ['ok' => false, 'filename' => null, 'message' => $message];
    }
}
