import { Head, router } from '@inertiajs/react';
import {
    AlertTriangle,
    ArrowLeft,
    ArrowRight,
    CheckCircle2,
    Clock,
    Flag,
    Loader2,
    Send,
} from 'lucide-react';
import { useCallback, useEffect, useMemo, useRef, useState } from 'react';

import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { submit } from '@/routes/cbt/attempts';
import { save } from '@/routes/cbt/attempts/answers';

type QuestionOption = {
    id: number;
    option_label: string;
    option_text: string;
};

type QuestionItem = {
    id: number;
    question_text: string;
    image_url: string | null;
    difficulty_level: string;
    selected_option_id: number | null;
    options: QuestionOption[];
    section_id?: number;
    section_title?: string;
    global_index?: number;
};

type ExamSectionItem = {
    id: number;
    title: string;
    duration_minutes: number | null;
    order_index: number | null;
    questions: QuestionItem[];
};

type ExamAttemptData = {
    attempt_id: number;
    status: string;
    started_at: string;
    total_duration_minutes: number;
    session: {
        id: number;
        title: string | null;
    };
    sections: ExamSectionItem[];
};

type PageProps = {
    attempt: {
        data: ExamAttemptData;
    };
};

function getXsrfToken(): string {
    if (typeof document === 'undefined') {
        return '';
    }

    const match = document.cookie.match(
        new RegExp('(^|;\\s*)XSRF-TOKEN=([^;]*)'),
    );

    return match ? decodeURIComponent(match[2]) : '';
}

export default function ExamSheet({ attempt: { data: examData } }: PageProps) {
    const attemptId = examData.attempt_id;

    // Flatten all questions across sections into a single sequential list
    const allQuestions: QuestionItem[] = useMemo(() => {
        let count = 0;

        return examData.sections.flatMap((section) =>
            section.questions.map((q) => ({
                ...q,
                section_id: section.id,
                section_title: section.title,
                global_index: count++,
            })),
        );
    }, [examData.sections]);

    const [currentIndex, setCurrentIndex] = useState(0);

    // Answers mapping: questionId -> selectedOptionId
    const [answers, setAnswers] = useState<Record<number, number | null>>(
        () => {
            const initial: Record<number, number | null> = {};
            allQuestions.forEach((q) => {
                initial[q.id] = q.selected_option_id;
            });

            return initial;
        },
    );

    // Marked as doubtful (ragu-ragu): Set of question IDs
    const [doubtfulIds, setDoubtfulIds] = useState<Set<number>>(new Set());

    // Autosave status: 'saved' | 'saving' | 'error'
    const [saveStatus, setSaveStatus] = useState<'saved' | 'saving' | 'error'>(
        'saved',
    );

    // Submit dialog state
    const [isSubmitDialogOpen, setIsSubmitDialogOpen] = useState(false);
    const [isSubmitting, setIsSubmitting] = useState(false);

    // Current active question
    const currentQuestion = allQuestions[currentIndex] || allQuestions[0];

    // Calculate remaining seconds from started_at and total_duration_minutes
    const calculateInitialSeconds = useCallback(() => {
        const startedTime = new Date(examData.started_at).getTime();
        const durationMs = examData.total_duration_minutes * 60 * 1000;
        const endTime = startedTime + durationMs;
        const now = Date.now();
        const diffSecs = Math.max(0, Math.floor((endTime - now) / 1000));

        return diffSecs;
    }, [examData.started_at, examData.total_duration_minutes]);

    const [remainingSeconds, setRemainingSeconds] = useState(
        calculateInitialSeconds,
    );

    const isAutoSubmittingRef = useRef(false);

    // Submit Attempt Handler
    const handleSubmitAttempt = useCallback(() => {
        if (isSubmitting || isAutoSubmittingRef.current) {
            return;
        }

        setIsSubmitting(true);
        isAutoSubmittingRef.current = true;

        router.post(
            submit.url(attemptId),
            {},
            {
                onFinish: () => {
                    setIsSubmitting(false);
                },
            },
        );
    }, [attemptId, isSubmitting]);

    // Countdown Timer Effect
    useEffect(() => {
        const timer = setInterval(() => {
            setRemainingSeconds((prev) => {
                if (prev <= 1) {
                    clearInterval(timer);
                    handleSubmitAttempt();

                    return 0;
                }

                return prev - 1;
            });
        }, 1000);

        return () => clearInterval(timer);
    }, [handleSubmitAttempt]);

    // Format remaining time into HH:MM:SS
    const formattedTime = useMemo(() => {
        const hours = Math.floor(remainingSeconds / 3600);
        const minutes = Math.floor((remainingSeconds % 3600) / 60);
        const seconds = remainingSeconds % 60;

        if (hours > 0) {
            return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }

        return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }, [remainingSeconds]);

    // Answer Selection & Autosave
    const handleSelectOption = async (optionId: number) => {
        if (!currentQuestion) {
            return;
        }

        // Toggle / Update local state
        const newOptionId =
            answers[currentQuestion.id] === optionId ? null : optionId;
        setAnswers((prev) => ({
            ...prev,
            [currentQuestion.id]: newOptionId,
        }));

        setSaveStatus('saving');

        try {
            const response = await fetch(save.url(attemptId), {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-XSRF-TOKEN': getXsrfToken(),
                },
                body: JSON.stringify({
                    question_id: currentQuestion.id,
                    selected_option_id: newOptionId,
                    time_spent_seconds: 5,
                }),
            });

            if (response.ok) {
                setSaveStatus('saved');
            } else {
                setSaveStatus('error');
            }
        } catch {
            setSaveStatus('error');
        }
    };

    // Toggle doubtful state for current question
    const handleToggleDoubtful = () => {
        if (!currentQuestion) {
            return;
        }

        setDoubtfulIds((prev) => {
            const next = new Set(prev);

            if (next.has(currentQuestion.id)) {
                next.delete(currentQuestion.id);
            } else {
                next.add(currentQuestion.id);
            }

            return next;
        });
    };

    // Counters for overview
    const stats = useMemo(() => {
        let answered = 0;
        let doubtful = 0;
        let unanswered = 0;

        allQuestions.forEach((q) => {
            const hasAnswer =
                answers[q.id] !== null && answers[q.id] !== undefined;
            const isDoubtful = doubtfulIds.has(q.id);

            if (isDoubtful) {
                doubtful++;
            }

            if (hasAnswer) {
                answered++;
            } else {
                unanswered++;
            }
        });

        return { answered, doubtful, unanswered, total: allQuestions.length };
    }, [allQuestions, answers, doubtfulIds]);

    const isTimerUrgent = remainingSeconds < 300; // < 5 minutes

    return (
        <div className="flex min-h-screen flex-col bg-background text-foreground">
            <Head title={`CBT: ${examData.session.title || 'Ujian'}`} />

            {/* Top Navigation Bar */}
            <header className="sticky top-0 z-40 border-b border-border bg-card/95 px-4 py-3 backdrop-blur sm:px-6">
                <div className="mx-auto flex max-w-7xl items-center justify-between gap-4">
                    <div className="min-w-0">
                        <h1 className="truncate text-base font-bold sm:text-lg">
                            {examData.session.title || 'Lembar Ujian CBT'}
                        </h1>
                        <p className="text-xs text-muted-foreground">
                            Subtes:{' '}
                            <span className="font-medium text-foreground">
                                {currentQuestion?.section_title}
                            </span>
                        </p>
                    </div>

                    <div className="flex items-center gap-3 sm:gap-6">
                        {/* Autosave Indicator */}
                        <div className="hidden items-center gap-1.5 text-xs text-muted-foreground sm:flex">
                            {saveStatus === 'saving' && (
                                <>
                                    <Loader2 className="h-3.5 w-3.5 animate-spin text-primary" />
                                    <span>Menyimpan...</span>
                                </>
                            )}
                            {saveStatus === 'saved' && (
                                <>
                                    <CheckCircle2 className="h-3.5 w-3.5 text-emerald-500" />
                                    <span>Tersimpan</span>
                                </>
                            )}
                            {saveStatus === 'error' && (
                                <>
                                    <AlertTriangle className="h-3.5 w-3.5 text-destructive" />
                                    <span className="text-destructive">
                                        Gagal sync
                                    </span>
                                </>
                            )}
                        </div>

                        {/* Countdown Timer */}
                        <div
                            className={`flex items-center gap-1.5 rounded-lg px-3 py-1.5 font-mono text-sm font-semibold tracking-wider sm:text-base ${
                                isTimerUrgent
                                    ? 'animate-pulse bg-destructive/10 text-destructive'
                                    : 'bg-muted text-foreground'
                            }`}
                        >
                            <Clock className="h-4 w-4" />
                            <span>{formattedTime}</span>
                        </div>

                        {/* Submit Button */}
                        <Button
                            variant="destructive"
                            size="sm"
                            onClick={() => setIsSubmitDialogOpen(true)}
                            className="gap-1.5"
                        >
                            <Send className="h-4 w-4" />
                            <span className="hidden sm:inline">Kumpulkan</span>
                        </Button>
                    </div>
                </div>
            </header>

            {/* Main Content Area */}
            <main className="mx-auto grid w-full max-w-7xl flex-1 gap-6 p-4 md:grid-cols-12 md:p-6">
                {/* Left Area: Question & Options (8 of 12 cols on desktop) */}
                <div className="flex flex-col space-y-6 md:col-span-8 lg:col-span-9">
                    {/* Section Switcher Tabs */}
                    <div className="flex overflow-x-auto border-b border-border pb-1">
                        {examData.sections.map((section) => {
                            const isCurrentSection =
                                currentQuestion?.section_id === section.id;
                            const firstQuestionIndex = allQuestions.findIndex(
                                (q) => q.section_id === section.id,
                            );

                            return (
                                <button
                                    key={section.id}
                                    type="button"
                                    onClick={() => {
                                        if (firstQuestionIndex !== -1) {
                                            setCurrentIndex(firstQuestionIndex);
                                        }
                                    }}
                                    className={`shrink-0 border-b-2 px-4 py-2 text-sm font-medium transition-colors ${
                                        isCurrentSection
                                            ? 'border-primary text-primary'
                                            : 'border-transparent text-muted-foreground hover:text-foreground'
                                    }`}
                                >
                                    {section.title}
                                </button>
                            );
                        })}
                    </div>

                    {/* Question Card */}
                    {currentQuestion && (
                        <Card className="flex-1 shadow-sm">
                            <CardContent className="space-y-6 p-6">
                                {/* Question Header */}
                                <div className="flex items-center justify-between border-b border-border pb-4">
                                    <div className="flex items-center gap-3">
                                        <span className="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-sm font-bold text-primary-foreground">
                                            {currentIndex + 1}
                                        </span>
                                        <span className="text-sm font-medium text-muted-foreground">
                                            Soal {currentIndex + 1} dari{' '}
                                            {allQuestions.length}
                                        </span>
                                    </div>

                                    <Badge
                                        variant="outline"
                                        className="text-xs uppercase"
                                    >
                                        {currentQuestion.difficulty_level}
                                    </Badge>
                                </div>

                                {/* Question Image if exists */}
                                {currentQuestion.image_url && (
                                    <div className="overflow-hidden rounded-lg border border-border">
                                        <img
                                            src={currentQuestion.image_url}
                                            alt={`Soal ${currentIndex + 1}`}
                                            className="max-h-80 w-full object-contain"
                                        />
                                    </div>
                                )}

                                {/* Question Text */}
                                <div className="text-base leading-relaxed sm:text-lg">
                                    <p className="whitespace-pre-line">
                                        {currentQuestion.question_text}
                                    </p>
                                </div>

                                <Separator />

                                {/* Option Choices */}
                                <div className="space-y-3 pt-2">
                                    {currentQuestion.options.map((option) => {
                                        const isSelected =
                                            answers[currentQuestion.id] ===
                                            option.id;

                                        return (
                                            <button
                                                key={option.id}
                                                type="button"
                                                onClick={() =>
                                                    handleSelectOption(
                                                        option.id,
                                                    )
                                                }
                                                className={`flex w-full items-start gap-4 rounded-xl border p-4 text-left transition-all ${
                                                    isSelected
                                                        ? 'border-primary bg-primary/5 text-foreground ring-1 ring-primary'
                                                        : 'border-border bg-card hover:border-primary/50 hover:bg-muted/30'
                                                }`}
                                            >
                                                <span
                                                    className={`flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold transition-colors ${
                                                        isSelected
                                                            ? 'bg-primary text-primary-foreground'
                                                            : 'border border-border bg-muted text-muted-foreground'
                                                    }`}
                                                >
                                                    {option.option_label}
                                                </span>

                                                <span className="pt-0.5 text-sm sm:text-base">
                                                    {option.option_text}
                                                </span>
                                            </button>
                                        );
                                    })}
                                </div>
                            </CardContent>
                        </Card>
                    )}

                    {/* Bottom Action Navigation */}
                    <div className="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-border bg-card p-4">
                        <Button
                            variant="outline"
                            onClick={() =>
                                setCurrentIndex((prev) => Math.max(0, prev - 1))
                            }
                            disabled={currentIndex === 0}
                            className="gap-2"
                        >
                            <ArrowLeft className="h-4 w-4" />
                            Sebelumnya
                        </Button>

                        <Button
                            variant={
                                currentQuestion &&
                                doubtfulIds.has(currentQuestion.id)
                                    ? 'default'
                                    : 'outline'
                            }
                            onClick={handleToggleDoubtful}
                            className={`gap-2 ${
                                currentQuestion &&
                                doubtfulIds.has(currentQuestion.id)
                                    ? 'bg-amber-500 text-white hover:bg-amber-600'
                                    : 'text-amber-600 hover:text-amber-700'
                            }`}
                        >
                            <Flag className="h-4 w-4" />
                            <span>
                                {currentQuestion &&
                                doubtfulIds.has(currentQuestion.id)
                                    ? 'Ditandai Ragu-ragu'
                                    : 'Tandai Ragu-ragu'}
                            </span>
                        </Button>

                        <Button
                            onClick={() =>
                                setCurrentIndex((prev) =>
                                    Math.min(allQuestions.length - 1, prev + 1),
                                )
                            }
                            disabled={currentIndex === allQuestions.length - 1}
                            className="gap-2"
                        >
                            Selanjutnya
                            <ArrowRight className="h-4 w-4" />
                        </Button>
                    </div>
                </div>

                {/* Right Area: Question Number Palette (4 of 12 cols on desktop) */}
                <div className="md:col-span-4 lg:col-span-3">
                    <Card className="sticky top-20 shadow-sm">
                        <CardContent className="space-y-5 p-5">
                            <div>
                                <h3 className="font-semibold">Navigasi Soal</h3>
                                <p className="text-xs text-muted-foreground">
                                    Klik nomor soal untuk melompat langsung.
                                </p>
                            </div>

                            {/* Summary Badges */}
                            <div className="grid grid-cols-3 gap-2 text-center text-xs">
                                <div className="rounded-lg bg-emerald-500/10 p-2 text-emerald-600 dark:text-emerald-400">
                                    <div className="font-bold">
                                        {stats.answered}
                                    </div>
                                    <div>Dijawab</div>
                                </div>
                                <div className="rounded-lg bg-amber-500/10 p-2 text-amber-600 dark:text-amber-400">
                                    <div className="font-bold">
                                        {stats.doubtful}
                                    </div>
                                    <div>Ragu-ragu</div>
                                </div>
                                <div className="rounded-lg bg-muted p-2 text-muted-foreground">
                                    <div className="font-bold">
                                        {stats.unanswered}
                                    </div>
                                    <div>Kosong</div>
                                </div>
                            </div>

                            <Separator />

                            {/* Number Grid */}
                            <div className="grid max-h-[50vh] grid-cols-5 gap-2 overflow-y-auto pr-1">
                                {allQuestions.map((q, idx) => {
                                    const isCurrent = idx === currentIndex;
                                    const isAnswered =
                                        answers[q.id] !== null &&
                                        answers[q.id] !== undefined;
                                    const isDoubtful = doubtfulIds.has(q.id);

                                    let btnStyle =
                                        'bg-card border-border text-foreground hover:bg-muted';

                                    if (isDoubtful) {
                                        btnStyle =
                                            'bg-amber-500 text-white border-amber-600 hover:bg-amber-600';
                                    } else if (isAnswered) {
                                        btnStyle =
                                            'bg-emerald-600 text-white border-emerald-700 hover:bg-emerald-700';
                                    }

                                    return (
                                        <button
                                            key={q.id}
                                            type="button"
                                            onClick={() => setCurrentIndex(idx)}
                                            className={`relative flex h-10 w-full items-center justify-center rounded-lg border text-xs font-bold transition-all ${btnStyle} ${
                                                isCurrent
                                                    ? 'ring-2 ring-primary ring-offset-2 ring-offset-background'
                                                    : ''
                                            }`}
                                        >
                                            {idx + 1}
                                        </button>
                                    );
                                })}
                            </div>

                            <Button
                                variant="destructive"
                                className="w-full gap-2"
                                onClick={() => setIsSubmitDialogOpen(true)}
                            >
                                <Send className="h-4 w-4" />
                                Kumpulkan Ujian
                            </Button>
                        </CardContent>
                    </Card>
                </div>
            </main>

            {/* Submit Confirmation Dialog */}
            <AlertDialog
                open={isSubmitDialogOpen}
                onOpenChange={setIsSubmitDialogOpen}
            >
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>
                            Kumpulkan Ujian Sekarang?
                        </AlertDialogTitle>
                        <AlertDialogDescription asChild>
                            <div className="space-y-3 pt-2 text-sm text-muted-foreground">
                                <p>
                                    Setelah mengumpulkan, Anda tidak dapat
                                    mengubah jawaban lagi. Pastikan seluruh soal
                                    telah Anda periksa dengan seksama.
                                </p>

                                <div className="space-y-1 rounded-lg border border-border bg-muted/40 p-3 text-xs">
                                    <div className="flex justify-between">
                                        <span>Total Soal:</span>
                                        <span className="font-semibold text-foreground">
                                            {stats.total}
                                        </span>
                                    </div>
                                    <div className="flex justify-between text-emerald-600 dark:text-emerald-400">
                                        <span>Sudah Dijawab:</span>
                                        <span className="font-semibold">
                                            {stats.answered}
                                        </span>
                                    </div>
                                    <div className="flex justify-between text-amber-600 dark:text-amber-400">
                                        <span>Ditandai Ragu-ragu:</span>
                                        <span className="font-semibold">
                                            {stats.doubtful}
                                        </span>
                                    </div>
                                    <div className="flex justify-between text-destructive">
                                        <span>Belum Dijawab:</span>
                                        <span className="font-semibold">
                                            {stats.unanswered}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </AlertDialogDescription>
                    </AlertDialogHeader>

                    <AlertDialogFooter>
                        <AlertDialogCancel disabled={isSubmitting}>
                            Periksa Kembali
                        </AlertDialogCancel>
                        <AlertDialogAction
                            variant="destructive"
                            onClick={handleSubmitAttempt}
                            disabled={isSubmitting}
                        >
                            {isSubmitting ? (
                                <>
                                    <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                                    Mengumpulkan...
                                </>
                            ) : (
                                'Ya, Kumpulkan Ujian'
                            )}
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </div>
    );
}
