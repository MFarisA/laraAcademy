import { Head, Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';

import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { index } from '@/routes/batches';

type Batch = {
    id: number;
    program_id: number;
    name: string;
    start_date: string;
    end_date: string;
    program?: { id: number; name: string; code?: string | null } | null;
    classrooms_count?: number;
};

type PageProps = {
    batch: {
        data: Batch;
    };
};

export default function Show({ batch: { data: batch } }: PageProps) {
    return (
        <>
            <Head title={`Detail Angkatan #${batch.id}`} />

            <h1 className="sr-only">Detail Angkatan</h1>

            <div className="space-y-6 px-6 py-6">
                {/* Header & Back Button */}
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <Heading
                        title="Detail Angkatan"
                        description={`Melihat informasi lengkap untuk angkatan #${batch.id}`}
                    />
                    <Button
                        variant="outline"
                        size="sm"
                        asChild
                        className="self-start"
                    >
                        <Link href={index.url()}>
                            <ArrowLeft className="mr-2 h-4 w-4" />
                            Kembali ke Daftar
                        </Link>
                    </Button>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle className="text-base font-semibold">
                            Informasi Angkatan
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div>
                            <span className="text-xs text-muted-foreground">
                                Nama
                            </span>
                            <p className="mt-1 font-medium">{batch.name}</p>
                        </div>

                        <Separator />

                        <div>
                            <span className="text-xs text-muted-foreground">
                                Program
                            </span>
                            <div className="mt-1">
                                <Badge variant="secondary">
                                    {batch.program?.name ?? '—'}
                                </Badge>
                            </div>
                        </div>

                        <Separator />

                        <div>
                            <span className="text-xs text-muted-foreground">
                                Mulai
                            </span>
                            <p className="mt-1 font-medium">
                                {new Date(batch.start_date).toLocaleDateString(
                                    'id-ID',
                                )}
                            </p>
                        </div>

                        <Separator />

                        <div>
                            <span className="text-xs text-muted-foreground">
                                Selesai
                            </span>
                            <p className="mt-1 font-medium">
                                {new Date(batch.end_date).toLocaleDateString(
                                    'id-ID',
                                )}
                            </p>
                        </div>

                        <Separator />

                        <div>
                            <span className="text-xs text-muted-foreground">
                                Jumlah Kelas
                            </span>
                            <p className="mt-1 font-medium">
                                {batch.classrooms_count ?? 0}
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
