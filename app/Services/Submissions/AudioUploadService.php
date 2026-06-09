<?php

namespace App\Services\Submissions;

use App\Models\Answer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AudioUploadService
{
    /** @var list<string> */
    private const ALLOWED_MIMES = [
        'audio/webm', 'audio/ogg', 'audio/mpeg', 'audio/mp3', 'audio/mp4',
        'audio/x-m4a', 'audio/wav', 'audio/x-wav', 'video/webm',
    ];

    public function store(Answer $answer, UploadedFile $file): string
    {
        if ($answer->audio_path) {
            Storage::disk('public')->delete($answer->audio_path);
        }

        $path = $file->store(
            'recordings/'.$answer->submission_id,
            'public',
        );

        $answer->update(['audio_path' => $path]);

        return $path;
    }

    /**
     * @return list<string>
     */
    public static function allowedMimes(): array
    {
        return self::ALLOWED_MIMES;
    }
}
