import { Head, Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';

import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { index } from '@/routes/classrooms';

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

type PageProps = {
    classroom: {
        data: Classroom;
    };
};

export default function Show({ classroom: { data: classroom } }: PageProps) {
    return (
        <>
            <Head title={`Detail Kelas #${classroom.id}`} />

            <h1 className="sr-only">Detail Kelas</h1>

            <div className="space-y-6 px-6 py-6">
                {/* Header & Back Button */}
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <Heading
                        title="Detail Kelas"
                        description={`Melihat informasi lengkap untuk kelas #${classroom.id}`}
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
                            Informasi Kelas
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div>
                            <span className="text-xs text-muted-foreground">
                                Nama
                            </span>
                            <p className="mt-1 font-medium">{classroom.name}</p>
                        </div>

                        <Separator />

                        <div>
                            <span className="text-xs text-muted-foreground">
                                Cabang
                            </span>
                            <div className="mt-1">
                                <Badge variant="secondary">
                                    {classroom.branch?.name ?? '—'}
                                </Badge>
                            </div>
                        </div>

                        <Separator />

                        <div>
                            <span className="text-xs text-muted-foreground">
                                Batch
                            </span>
                            <p className="mt-1 font-medium">
                                {classroom.batch?.name ?? '—'}
                            </p>
                        </div>

                        <Separator />

                        <div>
                            <span className="text-xs text-muted-foreground">
                                Kapasitas
                            </span>
                            <p className="mt-1 font-medium">
                                {classroom.capacity}
                            </p>
                        </div>

                        <Separator />

                        <div>
                            <span className="text-xs text-muted-foreground">
                                Terisi
                            </span>
                            <p className="mt-1 font-medium">
                                {classroom.students_count ?? 0}
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
