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
import { destroy, index, show } from '@/routes/classrooms';

type Classroom = {
    id: number;
    batch_id: number;
    branch_id: number;
    name: string;
    capacity: number;
    students_count?: number | null;
    batch?: { id: number; name: string } | null;
    branch?: { id: number; name: string } | null;
};

type PaginationMeta = {
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
};

type PageProps = {
    classrooms: {
        data: Classroom[];
        meta: PaginationMeta;
    };
};

export default function Index({ classrooms }: PageProps) {
    const { data, meta } = classrooms;

    /**
     * `mergeQuery` dipakai supaya query string lain tetap ada di URL. Kalau
     * pakai `query`, seluruh query string lama hilang setiap kali pindah
     * halaman.
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
            <Head title="Kelas" />

            <h1 className="sr-only">Kelas</h1>

            <div className="space-y-6 px-6 py-6">
                <Heading
                    title="Kelas"
                    description="Kelola kelas beserta cabang, angkatan, dan kapasitasnya."
                />

                <div className="overflow-hidden rounded-lg border border-border bg-card">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">No</TableHead>
                                <TableHead>Nama</TableHead>
                                <TableHead className="w-40">Cabang</TableHead>
                                <TableHead className="w-48">Batch</TableHead>
                                <TableHead className="w-24">
                                    Kapasitas
                                </TableHead>
                                <TableHead className="w-20">Terisi</TableHead>
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
                                        Belum ada kelas.
                                    </TableCell>
                                </TableRow>
                            ) : (
                                data.map((classroom, position) => {
                                    return (
                                        <TableRow
                                            key={classroom.id}
                                            className="hover:bg-muted/40"
                                        >
                                            <TableCell className="text-muted-foreground">
                                                {(meta.from ?? 1) + position}
                                            </TableCell>

                                            <TableCell>
                                                <Link
                                                    href={show.url(
                                                        classroom.id,
                                                    )}
                                                    className="block transition-colors hover:text-primary hover:underline"
                                                >
                                                    {classroom.name}
                                                </Link>
                                            </TableCell>

                                            <TableCell>
                                                <Badge variant="secondary">
                                                    {classroom.branch?.name ??
                                                        '—'}
                                                </Badge>
                                            </TableCell>

                                            <TableCell>
                                                {classroom.batch?.name ?? '—'}
                                            </TableCell>

                                            <TableCell className="text-muted-foreground">
                                                {classroom.capacity}
                                            </TableCell>

                                            <TableCell className="text-muted-foreground">
                                                {classroom.students_count ?? 0}
                                            </TableCell>

                                            <TableCell className="text-right">
                                                <DropdownMenu>
                                                    <DropdownMenuTrigger
                                                        asChild
                                                    >
                                                        <Button
                                                            variant="outline"
                                                            size="icon-sm"
                                                            aria-label="Aksi kelas"
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
                                                                        kelas
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
                                                                                classroom.id,
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
                            {meta.total} kelas
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
