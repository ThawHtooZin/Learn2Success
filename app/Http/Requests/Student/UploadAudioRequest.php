<?php

namespace App\Http\Requests\Student;

use App\Services\Submissions\AudioUploadService;
use Illuminate\Foundation\Http\FormRequest;

class UploadAudioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'audio' => [
                'required', 'file',
                'max:51200',
                'mimetypes:'.implode(',', AudioUploadService::allowedMimes()),
            ],
        ];
    }
}
