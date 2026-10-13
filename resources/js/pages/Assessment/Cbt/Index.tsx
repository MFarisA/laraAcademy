import { Head, Link, useForm } from '@inertiajs/react';
import { Calendar, Clock, KeyRound, PlayCircle } from 'lucide-react';
import { useState } from 'react';

import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { enter, index, start } from '@/routes/cbt/sessions';
import type { BreadcrumbItem } from '@/types';

type ExamSessionItem = {
    id: number;
    title: string | null;
    start_time: string;
    end_time: string;
    is_public?: boolean;
    template?: {
        id: number;
        title: string;
        total_duration_minutes?: number | null;
    } | null;
    classroom?: {
        id: number;
        name: string;
    } | null;
    attempts?: {
        id: number;
        status: string;
        total_score?: number | null;
        is_passed?: boolean | null;
    }[];
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
        data: ExamSessionItem[];
        meta?: PaginationMeta;
    };
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Ujian CBT',
        href: index.url(),
    },
];

export default function Index({ sessions }: PageProps) {
    const { data: sessionList } = sessions;
    const [selectedSession, setSelectedSession] =
        useState<ExamSessionItem | null>(null);

    const {
        data: formData,
        setData,
        post,
        processing,
        errors,
        reset,
        clearErrors,
    } = useForm({
        token: '',
    });

    const formatDateTime = (val: string): string => {
        return new Date(val).toLocaleString('id-ID', {
            dateStyle: 'medium',
            timeStyle: 'short',
        });
    };

    const handleOpenTokenDialog = (session: ExamSessionItem) => {
        setSelectedSession(session);
        reset();
        clearErrors();
    };

    const handleCloseTokenDialog = () => {
        setSelectedSession(null);
        reset();
        clearErrors();
    };

    const handleTokenSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        if (!selectedSession) {
            return;
        }

        post(enter.url(selectedSession.id), {
            onSuccess: () => {
                setSelectedSession(null);
            },
        });
    };

    const getSessionTimingStatus = (startTime: string, endTime: string) => {
        const now = new Date();
        const start = new Date(startTime);
        const end = new Date(endTime);

        if (now < start) {
            return 'upcoming';
        }

        if (now > end) {
            return 'ended';
        }

        return 'active';
    };

    return (
        <>
            <Head title="Portal Ujian CBT" />

            <h1 className="sr-only">Portal Ujian CBT</h1>

            <div className="space-y-6 px-6 py-6">
                <Heading
                    title="Portal Ujian CBT"
                    description="Daftar sesi ujian yang dijadwalkan untuk kelas atau akun Anda. Masukkan token untuk memulai ujian."
                />

                {sessionList.length === 0 ? (
                    <Card className="border-dashed py-12 text-center">
                        <CardContent>
                            <p className="text-muted-foreground">
                                Saat ini belum ada sesi ujian yang tersedia
                                untuk Anda.
                            </p>
                        </CardContent>
                    </Card>
                ) : (
                    <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        {sessionList.map((session) => {
                            const timing = getSessionTimingStatus(
                                session.start_time,
                                session.end_time,
                            );

                            const userAttempt = session.attempts?.[0];

                            return (
                                <Card
                                    key={session.id}
                                    className="flex flex-col justify-between transition-all hover:shadow-md"
                                >
                                    <CardHeader className="space-y-2">
                                        <div className="flex items-start justify-between gap-2">
                                            <Badge
                                                variant={
                                                    timing === 'active'
                                                        ? 'default'
                                                        : 'secondary'
                                                }
                                            >
                                                {timing === 'active' &&
                                                    'Berlangsung'}
                                                {timing === 'upcoming' &&
                                                    'Akan Datang'}
                                                {timing === 'ended' &&
                                                    'Selesai'}
                                            </Badge>
                                            {session.classroom && (
                                                <span className="text-xs font-medium text-muted-foreground">
                                                    {session.classroom.name}
                                                </span>
                                            )}
                                        </div>
                                        <CardTitle className="line-clamp-2 text-lg">
                                            {session.title ??
                                                session.template?.title ??
                                                'Ujian CBT'}
                                        </CardTitle>
                                        <CardDescription>
                                            {session.template?.title}
                                        </CardDescription>
                                    </CardHeader>

                                    <CardContent className="space-y-3 text-sm text-muted-foreground">
                                        <div className="flex items-center gap-2">
                                            <Clock className="h-4 w-4 shrink-0" />
                                            <span>
                                                {session.template
                                                    ?.total_duration_minutes
                                                    ? `${session.template.total_duration_minutes} Menit`
                                                    : 'Fleksibel'}
                                            </span>
                                        </div>

                                        <div className="flex items-center gap-2">
                                            <Calendar className="h-4 w-4 shrink-0" />
                                            <div className="flex flex-col text-xs">
                                                <span>
                                                    Mulai:{' '}
                                                    {formatDateTime(
                                                        session.start_time,
                                                    )}
                                                </span>
                                                <span>
                                                    Selesai:{' '}
                                                    {formatDateTime(
                                                        session.end_time,
                                                    )}
                                                </span>
                                            </div>
                                        </div>

                                        {userAttempt && (
                                            <div className="mt-2 rounded-md bg-muted p-2.5 text-xs">
                                                <div className="flex justify-between font-medium">
                                                    <span>Status Anda:</span>
                                                    <span className="capitalize">
                                                        {userAttempt.status ===
                                                        'in_progress'
                                                            ? 'Sedang Dikerjakan'
                                                            : userAttempt.status ===
                                                                'submitted'
                                                              ? 'Sudah Mengumpulkan'
                                                              : userAttempt.status}
                                                    </span>
                                                </div>
                                                {userAttempt.total_score !==
                                                    null &&
                                                    userAttempt.total_score !==
                                                        undefined && (
                                                        <div className="mt-1 flex justify-between font-semibold text-foreground">
                                                            <span>Nilai:</span>
                                                            <span>
                                                                {
                                                                    userAttempt.total_score
                                                                }
                                                            </span>
                                                        </div>
                                                    )}
                                            </div>
                                        )}
                                    </CardContent>

                                    <CardFooter className="pt-2">
                                        {userAttempt?.status ===
                                        'in_progress' ? (
                                            <Button
                                                asChild
                                                className="w-full gap-2"
                                            >
                                                <Link
                                                    href={start.url(session.id)}
                                                >
                                                    <PlayCircle className="h-4 w-4" />
                                                    Lanjutkan Ujian
                                                </Link>
                                            </Button>
                                        ) : userAttempt?.status ===
                                          'submitted' ? (
                                            <Button
                                                disabled
                                                variant="secondary"
                                                className="w-full"
                                            >
                                                Sudah Selesai
                                            </Button>
                                        ) : timing === 'active' ? (
                                            <Button
                                                className="w-full gap-2"
                                                onClick={() =>
                                                    handleOpenTokenDialog(
                                                        session,
                                                    )
                                                }
                                            >
                                                <KeyRound className="h-4 w-4" />
                                                Masukkan Token & Mulai
                                            </Button>
                                        ) : timing === 'upcoming' ? (
                                            <Button
                                                disabled
                                                variant="outline"
                                                className="w-full"
                                            >
                                                Belum Dimulai
                                            </Button>
                                        ) : (
                                            <Button
                                                disabled
                                                variant="outline"
                                                className="w-full"
                                            >
                                                Waktu Telah Berakhir
                                            </Button>
                                        )}
                                    </CardFooter>
                                </Card>
                            );
                        })}
                    </div>
                )}
            </div>

            {/* Dialog Masukkan Token */}
            <Dialog
                open={!!selectedSession}
                onOpenChange={(open) => !open && handleCloseTokenDialog()}
            >
                <DialogContent className="sm:max-w-md">
                    <DialogHeader>
                        <DialogTitle>Masukkan Token Sesi Ujian</DialogTitle>
                        <DialogDescription>
                            Masukkan token unik sesi ujian yang diberikan oleh
                            pengawas atau instruktur Anda.
                        </DialogDescription>
                    </DialogHeader>

                    <form onSubmit={handleTokenSubmit} className="space-y-4">
                        <div className="space-y-2">
                            <Label htmlFor="token">Token Ujian</Label>
                            <Input
                                id="token"
                                placeholder="Contoh: AB12CD"
                                value={formData.token}
                                onChange={(e) =>
                                    setData('token', e.target.value)
                                }
                                maxLength={10}
                                className="text-center font-mono text-lg tracking-widest uppercase"
                                autoFocus
                                required
                            />
                            {errors.token && (
                                <p className="text-sm text-destructive">
                                    {errors.token}
                                </p>
                            )}
                        </div>

                        <DialogFooter className="gap-2 sm:gap-0">
                            <Button
                                type="button"
                                variant="outline"
                                onClick={handleCloseTokenDialog}
                                disabled={processing}
                            >
                                Batal
                            </Button>
                            <Button
                                type="submit"
                                disabled={processing || !formData.token}
                            >
                                {processing
                                    ? 'Memverifikasi...'
                                    : 'Mulai Ujian'}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </>
    );
}

Index.layout = {
    breadcrumbs,
};
