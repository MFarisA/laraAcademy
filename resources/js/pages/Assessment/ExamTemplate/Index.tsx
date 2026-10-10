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
import { destroy, index, show } from '@/routes/exam-templates';

type ExamTemplate = {
    id: number;
    title: string;
    type: string | null;
    total_duration_minutes: number | null;
    program?: {
        id: number;
        name: string;
    } | null;
};

type PaginationMeta = {
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
};

type PageProps = {
    templates: {
        data: ExamTemplate[];
        meta: PaginationMeta;
    };
};

export default function Index({ templates }: PageProps) {
    const { data, meta } = templates;

    const formatDuration = (value?: number | null): string => {
        if (value === null || value === undefined) {
            return '—';
        }

        return `${value} menit`;
    };

    /**
     * `mergeQuery` dipakai supaya filter lain tetap ada di URL. Kalau pakai
     * `query`, seluruh query string lama hilang setiap kali pindah halaman.
     */
    const goToPage = (page: number) => {
        router.get(
            index.url({ mergeQuery: { page } }),
            {},
            { preserveScroll: true },
        );
    };

    return (
        <>
            <Head title="Template Ujian" />

            <h1 className="sr-only">Template Ujian</h1>

            <div className="space-y-6 px-6 py-6">
                <Heading
                    title="Template Ujian"
                    description="Kelola template ujian, bagian ujian, dan passing grade untuk setiap program."
                />

                <div className="overflow-hidden rounded-lg border border-border bg-card">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">No</TableHead>
                                <TableHead>Judul</TableHead>
                                <TableHead className="w-48">Program</TableHead>
                                <TableHead className="w-32">Jenis</TableHead>
                                <TableHead className="w-32">Durasi</TableHead>
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
                                        Belum ada template ujian.
                                    </TableCell>
                                </TableRow>
                            ) : (
                                data.map((template, position) => {
                                    return (
                                        <TableRow
                                            key={template.id}
                                            className="hover:bg-muted/40"
                                        >
                                            <TableCell className="text-muted-foreground">
                                                {(meta.from ?? 1) + position}
                                            </TableCell>

                                            <TableCell>
                                                <Link
                                                    href={show.url(template.id)}
                                                    className="line-clamp-2 block max-w-xl transition-colors hover:text-primary hover:underline"
                                                >
                                                    {template.title}
                                                </Link>
                                            </TableCell>

                                            <TableCell>
                                                <Badge variant="secondary">
                                                    {template.program?.name ??
                                                        '—'}
                                                </Badge>
                                            </TableCell>

                                            <TableCell>
                                                {template.type ?? '—'}
                                            </TableCell>

                                            <TableCell>
                                                {formatDuration(
                                                    template.total_duration_minutes,
                                                )}
                                            </TableCell>

                                            <TableCell className="text-right">
                                                <DropdownMenu>
                                                    <DropdownMenuTrigger
                                                        asChild
                                                    >
                                                        <Button
                                                            variant="outline"
                                                            size="icon-sm"
                                                            aria-label="Aksi template ujian"
                                                        >
                                                            <MoreVertical />
                                                        </Button>
                                                    </DropdownMenuTrigger>
                                                    <DropdownMenuContent align="end">
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
                                                                        template
                                                                        ujian
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
                                                                                template.id,
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
                            {meta.total} template
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
