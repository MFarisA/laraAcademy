<?php

namespace App\Http\Controllers\Assessment;

use App\Http\Controllers\Controller;
use App\Models\Assessment\Exam\ExamTemplate;
use Illuminate\Http\Request;

class ExamTemplateController extends Controller
{
    public function index(Request $request)
    {
        // TODO: Tampilkan daftar template ujian
    }

    public function create()
    {
        // TODO: Tampilkan form pembuatan template ujian
    }

    public function store(Request $request)
    {
        // TODO: Simpan template ujian baru beserta seksi & soalnya
    }

    public function show(ExamTemplate $examTemplate)
    {
        // TODO: Tampilkan detail template ujian
    }

    public function edit(ExamTemplate $examTemplate)
    {
        // TODO: Tampilkan form edit template ujian
    }

    public function update(Request $request, ExamTemplate $examTemplate)
    {
        // TODO: Update template ujian beserta seksi & soalnya
    }

    public function destroy(ExamTemplate $examTemplate)
    {
        // TODO: Hapus template ujian
    }
}
