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
import { destroy, index, show } from '@/routes/exam-sessions';

type ExamSession = {
    id: number;
    title: string | null;
    token: string | null;
    start_time: string;
    template?: {
        id: number;
        title: string;
    } | null;
    classroom?: {
        id: number;
        name: string;
    } | null;
    attempts_count?: number | null;
};

type PaginationMeta = {
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
};

type PageProps = {
    sessions: {
        data: ExamSession[];
        meta: PaginationMeta;
    };
    templates: {
        data: {
            id: number;
            title: string;
        }[];
    };
    classrooms: {
        data: {
            id: number;
            name: string;
        }[];
    };
};

export default function Index({ sessions }: PageProps) {
    const { data, meta } = sessions;

    const formatDateTime = (value: string | null): string => {
        if (!value) {
            return '—';
        }

        return new Date(value).toLocaleString('id-ID', {
            dateStyle: 'medium',
            timeStyle: 'short',
        });
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
            <Head title="Sesi Ujian" />

            <h1 className="sr-only">Sesi Ujian</h1>

            <div className="space-y-6 px-6 py-6">
                <Heading
                    title="Sesi Ujian"
                    description="Kelola sesi ujian, token peserta, dan jadwal pelaksanaannya."
                />

                <div className="overflow-hidden rounded-lg border border-border bg-card">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">No</TableHead>
                                <TableHead>Judul</TableHead>
                                <TableHead className="w-48">Template</TableHead>
                                <TableHead className="w-40">Kelas</TableHead>
                                <TableHead className="w-32">Token</TableHead>
                                <TableHead className="w-44">Mulai</TableHead>
                                <TableHead className="w-20">Peserta</TableHead>
                                <TableHead className="w-24 text-right">
                                    Aksi
                                </TableHead>
                            </TableRow>
                        </TableHeader>

                        <TableBody>
                            {data.length === 0 ? (
                                <TableRow>
                                    <TableCell
                                        colSpan={8}
                                        className="h-32 text-center text-muted-foreground"
                                    >
                                        Belum ada sesi ujian.
                                    </TableCell>
                                </TableRow>
                            ) : (
                                data.map((session, position) => {
                                    return (
                                        <TableRow
                                            key={session.id}
                                            className="hover:bg-muted/40"
                                        >
                                            <TableCell className="text-muted-foreground">
                                                {(meta.from ?? 1) + position}
                                            </TableCell>

                                            <TableCell>
                                                <Link
                                                    href={show.url(session.id)}
                                                    className="line-clamp-2 block max-w-xl transition-colors hover:text-primary hover:underline"
                                                >
                                                    {session.title ?? '—'}
                                                </Link>
                                            </TableCell>

                                            <TableCell>
                                                {session.template?.title ?? '—'}
                                            </TableCell>

                                            <TableCell>
                                                {session.classroom?.name ?? '—'}
                                            </TableCell>

                                            <TableCell>
                                                {session.token ? (
                                                    <code className="rounded bg-muted px-1.5 py-0.5 font-mono text-xs">
                                                        {session.token}
                                                    </code>
                                                ) : (
                                                    '—'
                                                )}
                                            </TableCell>

                                            <TableCell>
                                                {formatDateTime(
                                                    session.start_time,
                                                )}
                                            </TableCell>

                                            <TableCell>
                                                {session.attempts_count ?? 0}
                                            </TableCell>

                                            <TableCell className="text-right">
                                                <DropdownMenu>
                                                    <DropdownMenuTrigger
                                                        asChild
                                                    >
                                                        <Button
                                                            variant="outline"
                                                            size="icon-sm"
                                                            aria-label="Aksi sesi ujian"
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
                                                                        sesi
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
                                                                                session.id,
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
                            {meta.total} sesi
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
