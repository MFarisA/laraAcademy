import { Head, Link, router } from '@inertiajs/react';
import { MoreVertical, Plus } from 'lucide-react';

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
import { create, destroy, edit, index, show } from '@/routes/schedules';

type ScheduleRelation = {
    id: number;
    name: string;
};

type Schedule = {
    id: number;
    room_type: string | null;
    meeting_link: string | null;
    scheduled_at: string;
    duration_minutes: number | null;
    instructor?: ScheduleRelation | null;
    classroom?: ScheduleRelation | null;
    subject?: ScheduleRelation | null;
};

type PaginationMeta = {
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
};

type PageProps = {
    schedules: {
        data: Schedule[];
        meta: PaginationMeta;
    };
};

export default function Index({ schedules }: PageProps) {
    const { data, meta } = schedules;

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

    const formatScheduledAt = (value: string) => {
        return new Date(value).toLocaleString('id-ID', {
            dateStyle: 'medium',
            timeStyle: 'short',
        });
    };

    return (
        <>
            <Head title="Jadwal Kelas" />

            <h1 className="sr-only">Jadwal Kelas</h1>

            <div className="space-y-6 px-6 py-6">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <Heading
                        title="Jadwal Kelas"
                        description="Kelola jadwal pertemuan, ruang, dan pengajar untuk setiap kelas."
                    />
                    <Button asChild className="self-start">
                        <Link href={create.url()}>
                            <Plus />
                            Tambah Jadwal
                        </Link>
                    </Button>
                </div>

                <div className="overflow-hidden rounded-lg border border-border bg-card">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">No</TableHead>
                                <TableHead className="w-48">Jadwal</TableHead>
                                <TableHead>Mata Pelajaran</TableHead>
                                <TableHead className="w-40">Kelas</TableHead>
                                <TableHead className="w-32">
                                    Ruang/Jenis
                                </TableHead>
                                <TableHead className="w-28">Durasi</TableHead>
                                <TableHead className="w-24 text-right">
                                    Aksi
                                </TableHead>
                            </TableRow>
                        </TableHeader>

                        <TableBody>
                            {data.length === 0 ? (
                                <TableRow>
                                    <TableCell
                                        colSpan={7}
                                        className="h-32 text-center text-muted-foreground"
                                    >
                                        Belum ada jadwal kelas.
                                    </TableCell>
                                </TableRow>
                            ) : (
                                data.map((schedule, position) => {
                                    return (
                                        <TableRow
                                            key={schedule.id}
                                            className="hover:bg-muted/40"
                                        >
                                            <TableCell className="text-muted-foreground">
                                                {(meta.from ?? 1) + position}
                                            </TableCell>

                                            <TableCell>
                                                <Link
                                                    href={show.url(schedule.id)}
                                                    className="transition-colors hover:text-primary hover:underline"
                                                >
                                                    {formatScheduledAt(
                                                        schedule.scheduled_at,
                                                    )}
                                                </Link>
                                            </TableCell>

                                            <TableCell>
                                                <Badge variant="secondary">
                                                    {schedule.subject?.name ??
                                                        '—'}
                                                </Badge>
                                            </TableCell>

                                            <TableCell>
                                                {schedule.classroom?.name ??
                                                    '—'}
                                            </TableCell>

                                            <TableCell>
                                                {schedule.room_type ?? '—'}
                                            </TableCell>

                                            <TableCell>
                                                {schedule.duration_minutes !==
                                                null
                                                    ? `${schedule.duration_minutes} menit`
                                                    : '—'}
                                            </TableCell>

                                            <TableCell className="text-right">
                                                <DropdownMenu>
                                                    <DropdownMenuTrigger
                                                        asChild
                                                    >
                                                        <Button
                                                            variant="outline"
                                                            size="icon-sm"
                                                            aria-label="Aksi jadwal"
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
                                                                    schedule.id,
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
                                                                        jadwal
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
                                                                                schedule.id,
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
                            {meta.total} jadwal
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
                                Selanjutnya
                            </Button>
                        </div>
                    </div>
                )}
            </div>
        </>
    );
}
