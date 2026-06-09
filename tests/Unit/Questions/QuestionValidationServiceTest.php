<?php

namespace Tests\Unit\Questions;

use App\Services\Questions\QuestionValidationService;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class QuestionValidationServiceTest extends TestCase
{
    private QuestionValidationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new QuestionValidationService;
    }

    public function test_multiple_choice_requires_exactly_one_correct(): void
    {
        $this->expectException(ValidationException::class);

        $this->service->validate([
            'question_text' => 'Test?',
            'question_type' => 'multiple_choice',
            'choices' => ['A', 'B'],
            'correct_option_indexes' => [],
        ]);
    }

    public function test_recording_passes_without_choices(): void
    {
        $result = $this->service->validate([
            'question_text' => 'Speak',
            'question_type' => 'recording',
        ]);

        $this->assertNull($result['meta']);
    }
}
