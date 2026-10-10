import { Head, Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';

import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { index } from '@/routes/exam-sessions';

type ExamSession = {
    id: number;
    title: string | null;
    token: string | null;
    start_time: string;
    end_time: string;
    is_public?: boolean;
    classroom?: {
        id: number;
        name: string;
    } | null;
    attempts_count?: number | null;
};

type PageProps = {
    session: {
        data: ExamSession;
    };
};

export default function Show({ session: { data: examSession } }: PageProps) {
    const formatDateTime = (value: string | null): string => {
        if (!value) {
            return '—';
        }

        return new Date(value).toLocaleString('id-ID', {
            dateStyle: 'medium',
            timeStyle: 'short',
        });
    };

    return (
        <>
            <Head title={`Detail Sesi Ujian ${examSession.title ?? ''}`} />

            <h1 className="sr-only">Detail Sesi Ujian</h1>

            <div className="space-y-6 px-6 py-6">
                {/* Header & Back Button */}
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <Heading
                        title="Detail Sesi Ujian"
                        description={`Melihat informasi lengkap sesi ujian #${examSession.id}.`}
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
                            Informasi Sesi Ujian
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div>
                            <span className="text-xs text-muted-foreground">
                                Judul
                            </span>
                            <p className="mt-1 font-medium">
                                {examSession.title ?? '—'}
                            </p>
                        </div>

                        <Separator />

                        <div>
                            <span className="text-xs text-muted-foreground">
                                Token
                            </span>
                            {examSession.token ? (
                                <code className="mt-1 block w-fit rounded bg-muted px-1.5 py-0.5 font-mono text-xs">
                                    {examSession.token}
                                </code>
                            ) : (
                                <p className="mt-1 font-medium">—</p>
                            )}
                        </div>

                        <Separator />

                        <div>
                            <span className="text-xs text-muted-foreground">
                                Kelas
                            </span>
                            <p className="mt-1 font-medium">
                                {examSession.classroom?.name ?? '—'}
                            </p>
                        </div>

                        <Separator />

                        <div>
                            <span className="text-xs text-muted-foreground">
                                Mulai
                            </span>
                            <p className="mt-1 font-medium">
                                {formatDateTime(examSession.start_time)}
                            </p>
                        </div>

                        <Separator />

                        <div>
                            <span className="text-xs text-muted-foreground">
                                Selesai
                            </span>
                            <p className="mt-1 font-medium">
                                {formatDateTime(examSession.end_time)}
                            </p>
                        </div>

                        <Separator />

                        <div>
                            <span className="text-xs text-muted-foreground">
                                Publik
                            </span>
                            <p className="mt-1 font-medium">
                                {examSession.is_public ? 'Ya' : 'Tidak'}
                            </p>
                        </div>

                        <Separator />

                        <div>
                            <span className="text-xs text-muted-foreground">
                                Jumlah Peserta
                            </span>
                            <p className="mt-1 font-medium">
                                {examSession.attempts_count ?? 0}
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
