import { Head, Link, router } from '@inertiajs/react';
import { MoreVertical } from 'lucide-react';
import Heading from '@/components/heading';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { destroy, index, show } from '@/routes/questions';

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

type PaginationMeta = {
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
};

type PageProps = {
    questions: {
        data: Question[];
        meta: PaginationMeta;
    };
};

const GRADING_RULE_LABEL: Record<
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
            'border-emerald-500/30 bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800',
    },
    medium: {
        label: 'Sedang',
        className:
            'border-amber-500/30 bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800',
    },
    hard: {
        label: 'Sulit',
        className:
            'border-rose-500/30 bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-800',
    },
};

export default function Index({ questions }: PageProps) {
    const { data, meta } = questions;

    const goToPage = (page: number) => {
        router.get(index.url({ query: { page } }), { preserveScroll: true });
    };

    return (
        <>
            <Head title="Bank Soal" />

            <h1 className="sr-only">Bank Soal</h1>

            <div className="space-y-6 px-6 py-6">
                <Heading
                    title="Bank Soal"
                    description="Kelola soal-soal yang dipakai untuk ujian dan penilaian."
                />

                <div className="overflow-hidden rounded-lg border border-border bg-card">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">No</TableHead>
                                <TableHead className="w-48">
                                    Mata Pelajaran
                                </TableHead>
                                <TableHead>Soal</TableHead>
                                <TableHead className="w-32">Aturan</TableHead>
                                <TableHead className="w-32">Tingkat</TableHead>
                                <TableHead className="w-24 text-right">
                                    Aksi
                                </TableHead>
                            </TableRow>
                        </TableHeader>

                        <TableBody>
                            {data.length === 0 ? (
                                <TableRow>
                                    <TableCell
                                        colSpan={6}
                                        className="h-32 text-center text-muted-foreground"
                                    >
                                        Belum ada soal. Mulai tambahkan soal
                                        pertamamu.
                                    </TableCell>
                                </TableRow>
                            ) : (
                                data.map((question, position) => {
                                    const gradingRule =
                                        GRADING_RULE_LABEL[
                                            question.grading_rule
                                        ];
                                    const difficulty =
                                        DIFFICULTY_CONFIG[
                                            question.difficulty_level
                                        ];

                                    return (
                                        <TableRow key={question.id}>
                                            <TableCell className="text-muted-foreground">
                                                {(meta.from ?? 1) + position}
                                            </TableCell>

                                            <TableCell>
                                                <Badge variant="secondary">
                                                    {question.subject?.name ??
                                                        'Tanpa mapel'}
                                                </Badge>
                                            </TableCell>

                                            <TableCell>
                                                <p className="line-clamp-2 max-w-xl">
                                                    {question.question_text}
                                                </p>
                                            </TableCell>

                                            <TableCell>
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
                                            </TableCell>

                                            <TableCell>
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
                                            </TableCell>

                                            <TableCell className="text-right">
                                                <DropdownMenu>
                                                    <DropdownMenuTrigger
                                                        asChild
                                                    >
                                                        <Button
                                                            variant="outline"
                                                            size="icon-sm"
                                                            aria-label="Aksi soal"
                                                        >
                                                            <MoreVertical />
                                                        </Button>
                                                    </DropdownMenuTrigger>
                                                    <DropdownMenuContent align="end">
                                                        <DropdownMenuItem
                                                            asChild
                                                        >
                                                            <Link
                                                                href={show.url(
                                                                    question.id,
                                                                )}
                                                            >
                                                                Lihat
                                                            </Link>
                                                        </DropdownMenuItem>
                                                        <DropdownMenuSeparator />
                                                        <AlertDialog>
                                                            <AlertDialogTrigger
                                                                asChild
                                                            >
                                                                <DropdownMenuItem
                                                                    variant="destructive"
                                                                    onSelect={(
                                                                        event,
                                                                    ) =>
                                                                        event.preventDefault()
                                                                    }
                                                                >
                                                                    Hapus
                                                                </DropdownMenuItem>
                                                            </AlertDialogTrigger>
                                                            <AlertDialogContent>
                                                                <AlertDialogHeader>
                                                                    <AlertDialogTitle>
                                                                        Hapus
                                                                        soal
                                                                        ini?
                                                                    </AlertDialogTitle>
                                                                    <AlertDialogDescription>
                                                                        Tindakan
                                                                        ini
                                                                        tidak
                                                                        bisa
                                                                        dibatalkan.
                                                                    </AlertDialogDescription>
                                                                </AlertDialogHeader>
                                                                <AlertDialogFooter>
                                                                    <AlertDialogCancel>
                                                                        Batal
                                                                    </AlertDialogCancel>
                                                                    <AlertDialogAction
                                                                        variant="destructive"
                                                                        asChild
                                                                    >
                                                                        <Link
                                                                            href={destroy.url(
                                                                                question.id,
                                                                            )}
                                                                            method="delete"
                                                                            as="button"
                                                                        >
                                                                            Hapus
                                                                        </Link>
                                                                    </AlertDialogAction>
                                                                </AlertDialogFooter>
                                                            </AlertDialogContent>
                                                        </AlertDialog>
                                                    </DropdownMenuContent>
                                                </DropdownMenu>
                                            </TableCell>
                                        </TableRow>
                                    );
                                })
                            )}
                        </TableBody>
                    </Table>
                </div>

                {data.length > 0 && (
                    <div className="flex flex-wrap items-center justify-between gap-4">
                        <p className="text-sm text-muted-foreground">
                            Menampilkan {meta.from ?? 0}–{meta.to ?? 0} dari{' '}
                            {meta.total} soal
                        </p>

                        <div className="flex items-center gap-2">
                            <span className="text-sm text-muted-foreground">
                                Halaman {meta.current_page} dari{' '}
                                {meta.last_page}
                            </span>

                            <Button
                                variant="outline"
                                size="sm"
                                disabled={meta.current_page <= 1}
                                onClick={() => goToPage(meta.current_page - 1)}
                            >
                                Sebelumnya
                            </Button>

                            <Button
                                variant="outline"
                                size="sm"
                                disabled={meta.current_page >= meta.last_page}
                                onClick={() => goToPage(meta.current_page + 1)}
                            >
                                Berikutnya
                            </Button>
                        </div>
                    </div>
                )}
            </div>
        </>
    );
}
