<?php

namespace App\Actions\Assessment\Exam;

use App\Models\Assessment\Exam\ExamTemplate;
use Illuminate\Support\Facades\DB;

class SaveExamTemplateAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data, ?ExamTemplate $template = null): ExamTemplate
    {
        return DB::transaction(function () use ($data, $template) {
            $sections = $data['sections'] ?? null;
            unset($data['sections']);

            if ($template instanceof ExamTemplate) {
                $template->update($data);
            } else {
                $template = ExamTemplate::create($data);
            }

            if (\is_array($sections)) {
                $incomingIds = collect($sections)
                    ->pluck('id')
                    ->filter()
                    ->all();
                $template->sections()
                    ->whereNotIn('id', $incomingIds)
                    ->delete();
                foreach ($sections as $sectionData) {
                    $template->sections()->updateOrCreate(
                        ['id' => $sectionData['id'] ?? null],
                        $sectionData
                    );
                }
            }

            return $template->load(['program', 'sections.subject']);
        });
    }
}
