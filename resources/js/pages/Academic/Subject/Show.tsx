import { Head, Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';

import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { index } from '@/routes/subjects';

type Subject = {
    id: number;
    name: string;
    code: string | null;
    description: string | null;
};

type PageProps = {
    subject: {
        data: Subject;
    };
};

export default function Show({ subject: { data: subject } }: PageProps) {
    return (
        <>
            <Head title={`Detail Mata Pelajaran #${subject.id}`} />

            <h1 className="sr-only">Detail Mata Pelajaran</h1>

            <div className="space-y-6 px-6 py-6">
                {/* Header & Back Button */}
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <Heading
                        title="Detail Mata Pelajaran"
                        description={`Melihat informasi lengkap untuk mata pelajaran #${subject.id}`}
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
                            Informasi Mata Pelajaran
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div>
                            <span className="text-xs text-muted-foreground">
                                Nama
                            </span>
                            <p className="mt-1 font-medium">{subject.name}</p>
                        </div>

                        <Separator />

                        <div>
                            <span className="text-xs text-muted-foreground">
                                Kode
                            </span>
                            <p className="mt-1 font-medium">
                                {subject.code ?? '—'}
                            </p>
                        </div>

                        <Separator />

                        <div>
                            <span className="text-xs text-muted-foreground">
                                Deskripsi
                            </span>
                            <p className="mt-1 text-sm leading-relaxed whitespace-pre-wrap">
                                {subject.description ?? '—'}
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
