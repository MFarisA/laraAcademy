<?php

namespace App\Http\Requests\Assessment\Question;

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
            'subject_id' => ['sometimes', 'integer', 'exists:subjects,id'],
            'question_text' => ['sometimes', 'string'],
            'image_url' => ['nullable', 'string', 'max:2048'],
            'grading_rule' => ['sometimes', Rule::enum(GradingRuleEnum::class)],
            'difficulty_level' => ['sometimes', 'in:easy,medium,hard'],
            'options' => ['sometimes', 'array', 'min:1'],
            'options.*.id' => ['nullable', 'integer', 'exists:question_options,id'],
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

                $questionParam = $this->route('question');
                $question = $questionParam instanceof Question
                    ? $questionParam
                    : null;

                if (! $question && is_numeric($questionParam)) {
                    $found = Question::query()->whereKey((int) $questionParam)->first();
                    if ($found instanceof Question) {
                        $question = $found;
                    }
                }

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
                        if ($weight === null || (float) $weight < 1 || (float) $weight > 5) {
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
