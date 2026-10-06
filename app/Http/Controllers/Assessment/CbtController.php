<?php

namespace App\Http\Controllers\Assessment;

use App\Http\Controllers\Controller;
use App\Models\Assessment\Exam\ExamAttempt;
use App\Models\Assessment\Exam\ExamSession;
use Illuminate\Http\Request;

class CbtController extends Controller
{
    public function enter(Request $request, ExamSession $examSession)
    {
        // TODO: Validasi token sesi ujian yang diinput oleh siswa
    }

    public function start(Request $request, ExamSession $examSession)
    {
        // TODO: Inisialisasi atau resume attempt pengerjaan ujian dan tampilkan lembar soal
    }

    public function saveAnswer(Request $request, ExamAttempt $attempt)
    {
        // TODO: Autosave jawaban siswa untuk suatu soal (update/create ke exam_answers)
    }

    public function submit(Request $request, ExamAttempt $attempt)
    {
        // TODO: Kunci attempt ujian dan jalankan kalkulasi nilai akhir (scoring)
    }
}
