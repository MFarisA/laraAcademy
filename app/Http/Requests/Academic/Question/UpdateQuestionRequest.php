<?php

namespace App\Http\Requests\Academic\Question;

use App\Enum\Assessment\GradingRuleEnum;
use App\Models\Assessment\Question\Question;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateQuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'subject_id' => ['sometimes', 'required', 'exists:subjects,id'],
            'question_text' => ['sometimes', 'required', 'string'],
            'image_url' => ['nullable', 'string', 'max:2048'],
            'difficulty_level' => ['sometimes', 'required', 'in:easy,medium,hard'],
            'grading_rule' => ['sometimes', 'required', Rule::enum(GradingRuleEnum::class)],
            'options' => ['sometimes', 'required', 'array', 'min:2'],
            'options.*.option_label' => ['required_with:options', 'string', 'max:10'],
            'options.*.option_text' => ['required_with:options', 'string'],
            'options.*.is_correct' => ['nullable', 'boolean'],
            'options.*.weight_score' => ['nullable', 'numeric'],
        ];
    }

    /**
     * @return array<int, \Closure>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty() || ! $this->has('options')) {
                    return;
                }

                /** @var Question|null $question */
                $question = $this->route('question');

                $rule = GradingRuleEnum::tryFrom((string) $this->input('grading_rule'))
                    ?? $question?->grading_rule;
                $options = $this->input('options', []);

                if ($rule == GradingRuleEnum::STANDARD) {
                    $hasCorrect = \count(\array_filter($options, fn ($opt) => ! empty($opt['is_correct']))) > 0;
                    if (! $hasCorrect) {
                        $validator->errors()->add(
                            'options',
                            'Untuk tipe Standard, minimal harus ada 1 opsi jawaban yang benar (is_correct = true).'
                        );
                    }
                } elseif ($rule == GradingRuleEnum::TKP) {
                    foreach ($options as $index => $opt) {
                        $weight = $opt['weight_score'] ?? null;
                        if ($weight == null || (float) $weight < 1 || (float) $weight > 5) {
                            $validator->errors()->add(
                                "options.{$index}.weight_score",
                                'Untuk tipe TKP, setiap opsi harus memiliki bobot (weight_score) antara 1 sampai 5.'
                            );
                        }
                    }
                }
            },
        ];
    }
}
