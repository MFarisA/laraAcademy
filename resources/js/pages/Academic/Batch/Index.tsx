import { Head, Link, router } from '@inertiajs/react';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { index, show } from '@/routes/batches';

type Batch = {
    id: number;
    program_id: number;
    name: string;
    start_date: string;
    end_date: string;
    program?: { id: number; name: string; code?: string | null } | null;
    classrooms_count?: number;
};

type PaginationMeta = {
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
};

type PageProps = {
    batches: {
        data: Batch[];
        meta: PaginationMeta;
    };
};

export default function Index({ batches }: PageProps) {
    const { data, meta } = batches;

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
            <Head title="Angkatan" />

            <h1 className="sr-only">Angkatan</h1>

            <div className="space-y-6 px-6 py-6">
                <Heading
                    title="Angkatan"
                    description="Kelola angkatan beserta periode belajar dan jumlah kelasnya."
                />

                <div className="overflow-hidden rounded-lg border border-border bg-card">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">No</TableHead>
                                <TableHead>Nama</TableHead>
                                <TableHead className="w-48">Program</TableHead>
                                <TableHead className="w-32">Mulai</TableHead>
                                <TableHead className="w-32">Selesai</TableHead>
                                <TableHead className="w-24">
                                    Jumlah Kelas
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
                                        Belum ada angkatan.
                                    </TableCell>
                                </TableRow>
                            ) : (
                                data.map((batch, position) => {
                                    return (
                                        <TableRow
                                            key={batch.id}
                                            className="hover:bg-muted/40"
                                        >
                                            <TableCell className="text-muted-foreground">
                                                {(meta.from ?? 1) + position}
                                            </TableCell>

                                            <TableCell>
                                                <Link
                                                    href={show.url(batch.id)}
                                                    className="block transition-colors hover:text-primary hover:underline"
                                                >
                                                    {batch.name}
                                                </Link>
                                            </TableCell>

                                            <TableCell>
                                                <Badge variant="secondary">
                                                    {batch.program?.name ?? '—'}
                                                </Badge>
                                            </TableCell>

                                            <TableCell>
                                                {new Date(
                                                    batch.start_date,
                                                ).toLocaleDateString('id-ID')}
                                            </TableCell>

                                            <TableCell>
                                                {new Date(
                                                    batch.end_date,
                                                ).toLocaleDateString('id-ID')}
                                            </TableCell>

                                            <TableCell className="text-muted-foreground">
                                                {batch.classrooms_count ?? 0}
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
                            {meta.total} angkatan
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
