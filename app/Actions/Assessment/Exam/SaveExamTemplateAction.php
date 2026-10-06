<?php

namespace App\Actions\Assessment\Exam;

use App\Models\Assessment\Exam\ExamTemplate;

class SaveExamTemplateAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data, ?ExamTemplate $template = null): ExamTemplate
    {
        // TODO: Simpan template, seksi subtes (ExamSection), dan relasi soal (ExamSectionQuestion)
        return $template ?? new ExamTemplate;
    }
}
