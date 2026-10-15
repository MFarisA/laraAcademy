import { Head, Link } from '@inertiajs/react';
import {
    Award,
    CheckCircle2,
    ChevronLeft,
    Clock,
    FileText,
    MapPin,
    Trophy,
    XCircle,
} from 'lucide-react';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { index, leaderboard } from '@/routes/cbt/sessions';

interface SectionResult {
    id: number;
    score: string | number;
    is_passed: boolean;
    section: {
        id: number;
        title: string;
        passing_grade: number;
        duration_minutes: number;
    };
}

interface AttemptData {
    id: number;
    exam_session_id: number;
    total_score: string | number;
    is_passed: boolean;
    national_rank: number | null;
    branch_rank: number | null;
    started_at: string | null;
    submitted_at: string | null;
    duration_seconds?: number | null;
    section_results: SectionResult[];
    student: {
        id: number;
        name: string;
        branch?: {
            id: number;
            name: string;
        };
    };
}

interface ExamSessionData {
    id: number;
    title: string | null;
    template?: {
        id: number;
        title: string;
    };
}

interface PageProps {
    attempt: AttemptData;
    session: ExamSessionData;
}

export default function Result({ attempt, session }: PageProps) {
    return (
        <>
            <Head title={`Hasil Ujian - ${session.title ?? 'CBT'}`} />

            <div className="space-y-6 px-6 py-6">
                {/* Header Navigation */}
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <Button
                            asChild
                            variant="ghost"
                            size="sm"
                            className="mb-2 -ml-3 gap-1 text-muted-foreground"
                        >
                            <Link href={index.url()}>
                                <ChevronLeft className="h-4 w-4" />
                                Kembali ke Portal CBT
                            </Link>
                        </Button>
                        <Heading
                            title="Hasil Ujian CBT"
                            description={`Rangkuman penilaian untuk sesi "${session.title ?? session.template?.title ?? 'Ujian'}"`}
                        />
                    </div>

                    <div className="flex items-center gap-2">
                        <Button asChild variant="outline" className="gap-2">
                            <Link
                                href={leaderboard.url(attempt.exam_session_id)}
                            >
                                <Trophy className="h-4 w-4 text-amber-500" />
                                Lihat Leaderboard Sesi
                            </Link>
                        </Button>
                    </div>
                </div>

                {/* Score & Rank Summary Cards */}
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    {/* Status Kelulusan */}
                    <Card
                        className={
                            attempt.is_passed
                                ? 'border-emerald-500/30 bg-emerald-500/5'
                                : 'border-rose-500/30 bg-rose-500/5'
                        }
                    >
                        <CardHeader className="flex flex-row items-center justify-between pb-2">
                            <CardTitle className="text-sm font-medium">
                                Status Kelulusan
                            </CardTitle>
                            {attempt.is_passed ? (
                                <CheckCircle2 className="h-5 w-5 text-emerald-500" />
                            ) : (
                                <XCircle className="h-5 w-5 text-rose-500" />
                            )}
                        </CardHeader>
                        <CardContent>
                            <div
                                className={`text-2xl font-bold ${
                                    attempt.is_passed
                                        ? 'text-emerald-600 dark:text-emerald-400'
                                        : 'text-rose-600 dark:text-rose-400'
                                }`}
                            >
                                {attempt.is_passed ? 'LULUS' : 'TIDAK LULUS'}
                            </div>
                            <p className="text-xs text-muted-foreground">
                                {attempt.is_passed
                                    ? 'Memenuhi seluruh passing grade subtes'
                                    : 'Belum memenuhi passing grade minimum'}
                            </p>
                        </CardContent>
                    </Card>

                    {/* Skor Total */}
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between pb-2">
                            <CardTitle className="text-sm font-medium">
                                Total Skor
                            </CardTitle>
                            <Award className="h-5 w-5 text-primary" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">
                                {Number(attempt.total_score).toFixed(2)}
                            </div>
                            <p className="text-xs text-muted-foreground">
                                Akumulasi nilai dari seluruh subtes
                            </p>
                        </CardContent>
                    </Card>

                    {/* Ranking Nasional */}
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between pb-2">
                            <CardTitle className="text-sm font-medium">
                                Peringkat Nasional
                            </CardTitle>
                            <Trophy className="h-5 w-5 text-amber-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-amber-600 dark:text-amber-400">
                                {attempt.national_rank
                                    ? `#${attempt.national_rank}`
                                    : '-'}
                            </div>
                            <p className="text-xs text-muted-foreground">
                                Peringkat seluruh peserta sesi ini
                            </p>
                        </CardContent>
                    </Card>

                    {/* Ranking Cabang */}
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between pb-2">
                            <CardTitle className="text-sm font-medium">
                                Peringkat Cabang
                            </CardTitle>
                            <MapPin className="h-5 w-5 text-sky-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-sky-600 dark:text-sky-400">
                                {attempt.branch_rank
                                    ? `#${attempt.branch_rank}`
                                    : '-'}
                            </div>
                            <p className="text-xs text-muted-foreground">
                                {attempt.student?.branch?.name ?? 'Cabang Anda'}
                            </p>
                        </CardContent>
                    </Card>
                </div>

                {/* Subtest Breakdown */}
                <div className="space-y-4">
                    <div className="flex items-center gap-2">
                        <FileText className="h-5 w-5 text-muted-foreground" />
                        <h2 className="text-lg font-semibold">
                            Rincian Nilai per Subtes
                        </h2>
                    </div>

                    <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        {attempt.section_results?.map((res) => (
                            <Card key={res.id}>
                                <CardHeader className="space-y-1 pb-2">
                                    <div className="flex items-center justify-between">
                                        <Badge
                                            variant={
                                                res.is_passed
                                                    ? 'default'
                                                    : 'destructive'
                                            }
                                        >
                                            {res.is_passed
                                                ? 'Memenuhi'
                                                : 'Di Bawah Target'}
                                        </Badge>
                                        <div className="flex items-center gap-1 text-xs text-muted-foreground">
                                            <Clock className="h-3.5 w-3.5" />
                                            {res.section.duration_minutes} Menit
                                        </div>
                                    </div>
                                    <CardTitle className="text-base font-semibold">
                                        {res.section.title}
                                    </CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-2">
                                    <div className="flex items-baseline justify-between border-b pb-2">
                                        <span className="text-xs text-muted-foreground">
                                            Nilai Diperoleh
                                        </span>
                                        <span className="text-xl font-bold">
                                            {Number(res.score).toFixed(2)}
                                        </span>
                                    </div>
                                    <div className="flex items-center justify-between text-xs text-muted-foreground">
                                        <span>
                                            Ambang Batas (Passing Grade)
                                        </span>
                                        <span className="font-medium text-foreground">
                                            {res.section.passing_grade}
                                        </span>
                                    </div>
                                </CardContent>
                            </Card>
                        ))}
                    </div>
                </div>
            </div>
        </>
    );
}
