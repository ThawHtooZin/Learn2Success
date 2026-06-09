<?php

namespace Database\Seeders;

use App\Enums\QuestionType;
use App\Models\Quiz;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedLegacyQuizzes();
        $this->seedProgramQuizzes();
    }

    private function seedLegacyQuizzes(): void
    {
        $speaking = Quiz::query()->where('title', 'Speaking Basics')->first();

        if ($speaking) {
            $speaking->questions()->delete();
            $speaking->questions()->createMany([
                [
                    'question_text' => 'Introduce yourself in English.',
                    'question_type' => QuestionType::Recording,
                    'meta' => null,
                    'sort_order' => 0,
                ],
                [
                    'question_text' => 'Describe your favorite hobby.',
                    'question_type' => QuestionType::Recording,
                    'meta' => null,
                    'sort_order' => 1,
                ],
            ]);
        }

        $mc = Quiz::query()->where('title', 'Grammar MC Drill')->first();

        if ($mc) {
            $mc->questions()->delete();
            $mc->questions()->createMany([
                [
                    'question_text' => 'Choose the correct past tense: I ___ to school yesterday.',
                    'question_type' => QuestionType::MultipleChoice,
                    'meta' => [
                        'choices' => ['go', 'went', 'gone'],
                        'correct_option_indexes' => [1],
                    ],
                    'sort_order' => 0,
                ],
                [
                    'question_text' => 'Select the plural form of "child".',
                    'question_type' => QuestionType::MultipleChoice,
                    'meta' => [
                        'choices' => ['childs', 'children', 'childes'],
                        'correct_option_indexes' => [1],
                    ],
                    'sort_order' => 1,
                ],
                [
                    'question_text' => 'Pick the correct article: ___ apple a day.',
                    'question_type' => QuestionType::MultipleChoice,
                    'meta' => [
                        'choices' => ['A', 'An', 'The'],
                        'correct_option_indexes' => [1],
                    ],
                    'sort_order' => 2,
                ],
            ]);
        }
    }

    private function seedProgramQuizzes(): void
    {
        $programQuizzes = Quiz::query()
            ->whereNotNull('week_id')
            ->with('week')
            ->orderBy('week_id')
            ->orderBy('sort_order_in_week')
            ->get();

        foreach ($programQuizzes as $quiz) {
            $quiz->questions()->delete();
            $weekNumber = $quiz->week->week_number;
            $slot = $quiz->sort_order_in_week;

            $questions = match ($slot) {
                0, 1 => $this->multipleChoiceSet($weekNumber, $slot + 1),
                2 => $this->speakingSet($weekNumber),
                3 => $this->chooseAndSpeakSet($weekNumber),
                default => [],
            };

            foreach ($questions as $question) {
                $quiz->questions()->create($question);
            }
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function multipleChoiceSet(int $weekNumber, int $slot): array
    {
        $sets = [
            1 => [
                [
                    'question_text' => 'Choose the correct greeting: Good ___!',
                    'meta' => ['choices' => ['night', 'morning', 'sleep'], 'correct_option_indexes' => [1]],
                ],
                [
                    'question_text' => 'Pick the right pronoun: ___ am a student.',
                    'meta' => ['choices' => ['I', 'Me', 'My'], 'correct_option_indexes' => [0]],
                ],
                [
                    'question_text' => 'Select the plural: one cat, two ___.',
                    'meta' => ['choices' => ['cat', 'cats', 'cates'], 'correct_option_indexes' => [1]],
                ],
            ],
            2 => [
                [
                    'question_text' => 'Choose the past tense: She ___ to the park.',
                    'meta' => ['choices' => ['go', 'went', 'going'], 'correct_option_indexes' => [1]],
                ],
                [
                    'question_text' => 'Pick the correct article: ___ honest person.',
                    'meta' => ['choices' => ['A', 'An', 'The'], 'correct_option_indexes' => [1]],
                ],
                [
                    'question_text' => 'Select the right form: They ___ happy today.',
                    'meta' => ['choices' => ['is', 'are', 'am'], 'correct_option_indexes' => [1]],
                ],
            ],
        ];

        $items = $sets[$slot];

        return collect($items)->map(function (array $item, int $order) use ($weekNumber) {
            return [
                'question_text' => "[Week {$weekNumber}] {$item['question_text']}",
                'question_type' => QuestionType::MultipleChoice,
                'meta' => $item['meta'],
                'sort_order' => $order,
            ];
        })->all();
    }

    /**
     * Two speaking (recording) questions per week — slot 3.
     *
     * @return array<int, array<string, mixed>>
     */
    private function speakingSet(int $weekNumber): array
    {
        $prompts = [
            1 => [
                'Introduce yourself: say your name and where you are from.',
                'Say three things you like about learning English.',
            ],
            2 => [
                'Describe what you did yesterday in two or three sentences.',
                'Tell us about your favorite subject at school and why.',
            ],
            3 => [
                'Explain your morning routine step by step.',
                'Describe a place you want to visit and why.',
            ],
            4 => [
                'Talk about a goal you want to achieve this month.',
                'Give advice to a friend who is nervous about speaking English.',
            ],
        ];

        return collect($prompts[$weekNumber])->map(function (string $text, int $order) use ($weekNumber) {
            return [
                'question_text' => $text,
                'question_type' => QuestionType::Recording,
                'meta' => null,
                'sort_order' => $order,
            ];
        })->all();
    }

    /**
     * Two MC + one recording — only Choose and Speak types.
     *
     * @return array<int, array<string, mixed>>
     */
    private function chooseAndSpeakSet(int $weekNumber): array
    {
        $recordingPrompts = [
            1 => 'Read this aloud: "Hello, my name is ___. Nice to meet you."',
            2 => 'Read this aloud: "Yesterday I went to school and learned something new."',
            3 => 'Read this aloud: "Every morning I wake up and get ready for the day."',
            4 => 'Read this aloud: "I will keep practicing English every single day."',
        ];

        return [
            [
                'question_text' => "[Week {$weekNumber}] Choose the best reply: \"How are you?\"",
                'question_type' => QuestionType::MultipleChoice,
                'meta' => [
                    'choices' => ['I am fine, thank you.', 'I am 12 years old.', 'My name is Sam.'],
                    'correct_option_indexes' => [0],
                ],
                'sort_order' => 0,
            ],
            [
                'question_text' => "[Week {$weekNumber}] Pick the correct word order.",
                'question_type' => QuestionType::MultipleChoice,
                'meta' => [
                    'choices' => ['She reads books every day.', 'Reads she books every day.', 'Every day books she reads.'],
                    'correct_option_indexes' => [0],
                ],
                'sort_order' => 1,
            ],
            [
                'question_text' => $recordingPrompts[$weekNumber],
                'question_type' => QuestionType::Recording,
                'meta' => null,
                'sort_order' => 2,
            ],
        ];
    }
}
