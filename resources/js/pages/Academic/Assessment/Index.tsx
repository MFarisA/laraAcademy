import { Head, Link, router } from '@inertiajs/react';
import { MoreVertical, Plus } from 'lucide-react';
import Heading from '@/components/heading';
import {
    DifficultyBadge,
    GradingRuleBadge,
} from '@/components/question-badges';
import QuestionFilterBar from '@/components/question-filter-bar';
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
import { create, destroy, edit, index, show } from '@/routes/questions';

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
    subjects: {
        data: Subject[];
    };
    filters: {
        search: string | null;
        subject_id: string | null;
        difficulty_level: string | null;
        grading_rule: string | null;
        per_page: number;
    };
};

export default function Index({ questions, subjects, filters }: PageProps) {
    const { data, meta } = questions;

    const hasActiveFilter =
        filters.search !== null ||
        filters.subject_id !== null ||
        filters.difficulty_level !== null ||
        filters.grading_rule !== null;

    /**
     * `mergeQuery` dipakai supaya filter lain tetap ada di URL. Kalau pakai
     * `query`, seluruh query string lama hilang setiap kali pindah halaman.
     */
    const goToPage = (page: number) => {
        router.get(index.url({ mergeQuery: { page } }), {
            preserveScroll: true,
        });
    };

    return (
        <>
            <Head title="Bank Soal" />

            <h1 className="sr-only">Bank Soal</h1>

            <div className="space-y-6 px-6 py-6">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <Heading
                        title="Bank Soal"
                        description="Kelola soal-soal yang dipakai untuk ujian dan penilaian."
                    />
                    <Button asChild className="self-start">
                        <Link href={create.url()}>
                            <Plus />
                            Tambah Soal
                        </Link>
                    </Button>
                </div>

                <QuestionFilterBar subjects={subjects.data} filters={filters} />

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
                                        {hasActiveFilter
                                            ? 'Tidak ada soal yang cocok dengan filter ini. Coba ubah kata kunci atau tekan Reset.'
                                            : 'Belum ada soal. Mulai tambahkan soal pertamamu.'}
                                    </TableCell>
                                </TableRow>
                            ) : (
                                data.map((question, position) => {
                                    return (
                                        <TableRow
                                            key={question.id}
                                            className="hover:bg-muted/40"
                                        >
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
                                                <Link
                                                    href={show.url(question.id)}
                                                    className="line-clamp-2 block max-w-xl transition-colors hover:text-primary hover:underline"
                                                >
                                                    {question.question_text}
                                                </Link>
                                            </TableCell>

                                            <TableCell>
                                                <GradingRuleBadge
                                                    gradingRule={
                                                        question.grading_rule
                                                    }
                                                />
                                            </TableCell>

                                            <TableCell>
                                                <DifficultyBadge
                                                    difficultyLevel={
                                                        question.difficulty_level
                                                    }
                                                />
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
                                                                href={edit.url(
                                                                    question.id,
                                                                )}
                                                            >
                                                                Edit
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
