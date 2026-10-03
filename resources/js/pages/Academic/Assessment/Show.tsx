import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, Award, CheckCircle2 } from 'lucide-react';

import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { index } from '@/routes/questions';

type Subject = {
    id: number;
    name: string;
    code: string | null;
};

type QuestionOption = {
    id: number;
    option_label: string;
    option_text: string;
    is_correct: boolean;
    weight_score: number;
};

type Question = {
    id: number;
    subject_id: number;
    subject: Subject;
    question_text: string;
    image_url: string | null;
    grading_rule: 'STANDARD' | 'TKP';
    difficulty_level: string;
    options: QuestionOption[];
};

type PageProps = {
    question: {
        data: Question;
    };
};

const GRADING_RULE_CONFIG: Record<
    Question['grading_rule'],
    {
        label: string;
        className: string;
    }
> = {
    STANDARD: {
        label: 'Standard',
        className:
            'border-blue-500/30 bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400 dark:border-blue-800',
    },
    TKP: {
        label: 'TKP',
        className:
            'border-purple-500/30 bg-purple-50 text-purple-700 dark:bg-purple-950/40 dark:text-purple-400 dark:border-purple-800',
    },
};

const DIFFICULTY_CONFIG: Record<
    string,
    {
        label: string;
        className: string;
    }
> = {
    easy: {
        label: 'Mudah',
        className:
            'border-emerald-500/30 bg-emerald-50 text-emerald-700 dark:border-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800',
    },
    medium: {
        label: 'Sedang',
        className:
            'border-amber-500/30 bg-amber-50 text-amber-700 dark:border-amber-950/40 dark:text-amber-400 dark:border-amber-800',
    },
    hard: {
        label: 'Sulit',
        className:
            'border-rose-500/30 bg-rose-50 text-rose-700 dark:border-rose-950/40 dark:text-rose-400 dark:border-rose-800',
    },
};

export default function Show({ question: { data: question } }: PageProps) {
    const gradingRule = GRADING_RULE_CONFIG[question.grading_rule];
    const difficulty = DIFFICULTY_CONFIG[question.difficulty_level];
    const isTkp = question.grading_rule === 'TKP';

    return (
        <>
            <Head title={`Detail Soal #${question.id}`} />

            <h1 className="sr-only">Detail Soal</h1>

            <div className="space-y-6">
                {/* Header & Back Button */}
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <Heading
                        title="Detail Soal"
                        description={`Melihat informasi lengkap dan pilihan jawaban untuk soal #${question.id}`}
                    />
                    <Button
                        variant="outline"
                        size="sm"
                        asChild
                        className="self-start"
                    >
                        <Link href={index.url()}>
                            <ArrowLeft className="mr-2 h-4 w-4" />
                            Kembali ke Daftar
                        </Link>
                    </Button>
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    {/* Konten Soal & Opsi Jawaban (2 Kolom di Desktop) */}
                    <div className="space-y-6 lg:col-span-2">
                        <Card>
                            <CardHeader>
                                <CardTitle className="text-base font-semibold text-muted-foreground">
                                    Pertanyaan
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <p className="text-base leading-relaxed whitespace-pre-wrap">
                                    {question.question_text}
                                </p>

                                {question.image_url && (
                                    <div className="overflow-hidden rounded-lg border border-border">
                                        <img
                                            src={question.image_url}
                                            alt="Lampiran Soal"
                                            className="max-h-96 w-full bg-muted/40 object-contain"
                                        />
                                    </div>
                                )}
                            </CardContent>
                        </Card>

                        {/* Pilihan Jawaban */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="text-base font-semibold text-muted-foreground">
                                    Pilihan Jawaban
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-3">
                                {question.options.length === 0 ? (
                                    <p className="text-sm text-muted-foreground">
                                        Belum ada pilihan jawaban yang
                                        ditambahkan.
                                    </p>
                                ) : (
                                    question.options.map((option) => {
                                        const isCorrect = option.is_correct;

                                        return (
                                            <div
                                                key={option.id}
                                                className={`flex items-start justify-between gap-4 rounded-lg border p-4 transition-colors ${
                                                    !isTkp && isCorrect
                                                        ? 'border-emerald-500/50 bg-emerald-50/50 dark:border-emerald-800 dark:bg-emerald-950/20'
                                                        : 'border-border bg-card'
                                                }`}
                                            >
                                                <div className="flex items-start gap-3">
                                                    <span className="flex h-7 w-7 shrink-0 items-center justify-center rounded-full border border-border bg-muted text-sm font-semibold">
                                                        {option.option_label}
                                                    </span>
                                                    <span className="pt-0.5 text-sm leading-relaxed">
                                                        {option.option_text}
                                                    </span>
                                                </div>

                                                {/* Kunci jawaban hanya relevan
                                                    untuk STANDARD; TKP memakai
                                                    bobot skor. */}
                                                {!isTkp && isCorrect && (
                                                    <Badge
                                                        variant="outline"
                                                        className="shrink-0 border-emerald-500/30 bg-emerald-50 text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-400"
                                                    >
                                                        <CheckCircle2 className="mr-1 h-3.5 w-3.5" />
                                                        Kunci
                                                    </Badge>
                                                )}

                                                {isTkp && (
                                                    <Badge
                                                        variant="secondary"
                                                        className="shrink-0 gap-1"
                                                    >
                                                        <Award className="h-3 w-3" />
                                                        Poin:{' '}
                                                        {option.weight_score}
                                                    </Badge>
                                                )}
                                            </div>
                                        );
                                    })
                                )}
                            </CardContent>
                        </Card>
                    </div>

                    {/* Meta Informasi Soal (1 Kolom Sidebar) */}
                    <div>
                        <Card>
                            <CardHeader>
                                <CardTitle className="text-base font-semibold">
                                    Informasi Soal
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div>
                                    <span className="text-xs text-muted-foreground">
                                        Mata Pelajaran
                                    </span>
                                    <p className="mt-1 font-medium">
                                        {question.subject.name}
                                    </p>
                                    {question.subject.code && (
                                        <p className="text-xs text-muted-foreground">
                                            Kode: {question.subject.code}
                                        </p>
                                    )}
                                </div>

                                <Separator />

                                <div>
                                    <span className="text-xs text-muted-foreground">
                                        Aturan Penilaian
                                    </span>
                                    <div className="mt-1">
                                        <Badge
                                            variant="outline"
                                            className={
                                                gradingRule?.className ??
                                                'text-muted-foreground'
                                            }
                                        >
                                            {gradingRule?.label ??
                                                question.grading_rule}
                                        </Badge>
                                    </div>
                                    <p className="mt-1.5 text-xs text-muted-foreground">
                                        {isTkp
                                            ? 'Setiap pilihan memiliki bobot skor (skala 1-5).'
                                            : 'Menggunakan 1 kunci jawaban benar.'}
                                    </p>
                                </div>

                                <Separator />

                                <div>
                                    <span className="text-xs text-muted-foreground">
                                        Tingkat Kesulitan
                                    </span>
                                    <div className="mt-1">
                                        <Badge
                                            variant="outline"
                                            className={
                                                difficulty?.className ??
                                                'text-muted-foreground'
                                            }
                                        >
                                            {difficulty?.label ??
                                                question.difficulty_level}
                                        </Badge>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </>
    );
}
