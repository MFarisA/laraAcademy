<?php

namespace App\Http\Controllers\Assessment;

use App\Http\Controllers\Controller;
use App\Models\Assessment\Exam\ExamSession;
use Illuminate\Http\Request;

class ExamSessionController extends Controller
{
    public function index(Request $request)
    {
        // TODO: Tampilkan daftar sesi ujian
    }

    public function create()
    {
        // TODO: Tampilkan form pembuatan sesi ujian
    }

    public function store(Request $request)
    {
        // TODO: Simpan sesi ujian baru dan generate token
    }

    public function show(ExamSession $examSession)
    {
        // TODO: Tampilkan detail sesi ujian
    }

    public function edit(ExamSession $examSession)
    {
        // TODO: Tampilkan form edit sesi ujian
    }

    public function update(Request $request, ExamSession $examSession)
    {
        // TODO: Update sesi ujian
    }

    public function destroy(ExamSession $examSession)
    {
        // TODO: Hapus sesi ujian
    }

    public function regenerateToken(ExamSession $examSession)
    {
        // TODO: Generate ulang token sesi ujian yang baru
    }
}
