import { Head, router } from '@inertiajs/react';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import subjects from '@/routes/subjects';

type LearningMaterial = {
    subject_id: number;
    title: string;
    type: string | null;
    is_downloadable: boolean | null;
    preview_url: string | null;
    created_at: string | null;
};

type PaginationMeta = {
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
};

type PageProps = {
    material: {
        data: LearningMaterial[];
        meta: PaginationMeta;
    };
};

export default function Index({ material }: PageProps) {
    const { data, meta } = material;

    const formatDate = (value: string | null): string => {
        if (!value) {
            return '—';
        }

        return new Date(value).toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        });
    };

    /**
     * Halaman ini berada di `/subjects/{subject}/materials`, jadi subject id
     * diambil dari data materi pertama. `mergeQuery` dipakai supaya query
     * string lama tidak hilang setiap kali pindah halaman.
     */
    const handleChangePage = (page: number) => {
        const firstMaterial = data[0];

        if (!firstMaterial) {
            return;
        }

        router.get(
            subjects.materials.index.url(firstMaterial.subject_id, {
                mergeQuery: { page },
            }),
            {},
            { preserveScroll: true },
        );
    };

    return (
        <>
            <Head title="Materi Pembelajaran" />

            <h1 className="sr-only">Materi Pembelajaran</h1>

            <div className="space-y-6 px-6 py-6">
                <Heading
                    title="Materi Pembelajaran"
                    description="Daftar materi yang tersedia untuk mata pelajaran ini."
                />

                <div className="overflow-hidden rounded-lg border border-border bg-card">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">No</TableHead>
                                <TableHead>Judul</TableHead>
                                <TableHead className="w-32">Jenis</TableHead>
                                <TableHead className="w-40">Tanggal</TableHead>
                                <TableHead className="w-24">Unduh</TableHead>
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
                                        Belum ada materi pembelajaran.
                                    </TableCell>
                                </TableRow>
                            ) : (
                                data.map((item, position) => {
                                    return (
                                        <TableRow
                                            key={`${item.created_at}-${position}`}
                                            className="hover:bg-muted/40"
                                        >
                                            <TableCell className="text-muted-foreground">
                                                {(meta.from ?? 1) + position}
                                            </TableCell>

                                            <TableCell>
                                                <span className="line-clamp-2 block max-w-xl">
                                                    {item.title}
                                                </span>
                                            </TableCell>

                                            <TableCell>
                                                {item.type ?? '—'}
                                            </TableCell>

                                            <TableCell>
                                                {formatDate(item.created_at)}
                                            </TableCell>

                                            <TableCell>
                                                {item.is_downloadable
                                                    ? 'Ya'
                                                    : 'Tidak'}
                                            </TableCell>

                                            <TableCell className="text-right">
                                                {item.preview_url ? (
                                                    <a
                                                        href={item.preview_url}
                                                        target="_blank"
                                                        rel="noreferrer"
                                                        className="transition-colors hover:text-primary hover:underline"
                                                    >
                                                        Buka
                                                    </a>
                                                ) : (
                                                    <span className="text-muted-foreground">
                                                        —
                                                    </span>
                                                )}
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
                            {meta.total} materi
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
                                onClick={() =>
                                    handleChangePage(meta.current_page - 1)
                                }
                            >
                                Sebelumnya
                            </Button>

                            <Button
                                variant="outline"
                                size="sm"
                                disabled={meta.current_page >= meta.last_page}
                                onClick={() =>
                                    handleChangePage(meta.current_page + 1)
                                }
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
