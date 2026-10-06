<?php

namespace App\Http\Resources\Assessment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CbtExamSheetResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // TODO: Transformasi data soal ujian siswa tanpa membocorkan is_correct & weight_score
        return parent::toArray($request);
    }
}
