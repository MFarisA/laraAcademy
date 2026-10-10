import { Head, Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';

import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { index } from '@/routes/schedules';

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

type PageProps = {
    schedule: {
        data: Schedule;
    };
};

export default function Show({ schedule: { data: schedule } }: PageProps) {
    const formatScheduledAt = (value: string) => {
        return new Date(value).toLocaleString('id-ID', {
            dateStyle: 'medium',
            timeStyle: 'short',
        });
    };

    return (
        <>
            <Head title={`Detail Jadwal #${schedule.id}`} />

            <h1 className="sr-only">Detail Jadwal Kelas</h1>

            <div className="space-y-6 px-6 py-6">
                {/* Header & Back Button */}
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <Heading
                        title="Detail Jadwal Kelas"
                        description={`Melihat informasi lengkap untuk jadwal #${schedule.id}.`}
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

                <div className="max-w-2xl">
                    <Card>
                        <CardHeader>
                            <CardTitle className="text-base font-semibold">
                                Informasi Jadwal
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div>
                                <span className="text-xs text-muted-foreground">
                                    Jadwal
                                </span>
                                <p className="mt-1 font-medium">
                                    {formatScheduledAt(schedule.scheduled_at)}
                                </p>
                            </div>

                            <Separator />

                            <div>
                                <span className="text-xs text-muted-foreground">
                                    Mata Pelajaran
                                </span>
                                <div className="mt-1">
                                    <Badge variant="secondary">
                                        {schedule.subject?.name ?? '—'}
                                    </Badge>
                                </div>
                            </div>

                            <Separator />

                            <div>
                                <span className="text-xs text-muted-foreground">
                                    Kelas
                                </span>
                                <p className="mt-1 font-medium">
                                    {schedule.classroom?.name ?? '—'}
                                </p>
                            </div>

                            <Separator />

                            <div>
                                <span className="text-xs text-muted-foreground">
                                    Pengajar
                                </span>
                                <p className="mt-1 font-medium">
                                    {schedule.instructor?.name ?? '—'}
                                </p>
                            </div>

                            <Separator />

                            <div>
                                <span className="text-xs text-muted-foreground">
                                    Jenis Ruang
                                </span>
                                <p className="mt-1 font-medium">
                                    {schedule.room_type ?? '—'}
                                </p>
                            </div>

                            <Separator />

                            <div>
                                <span className="text-xs text-muted-foreground">
                                    Tautan Rapat
                                </span>

                                {schedule.meeting_link ? (
                                    <a
                                        href={schedule.meeting_link}
                                        target="_blank"
                                        rel="noreferrer"
                                        className="mt-1 block break-all text-primary hover:underline"
                                    >
                                        {schedule.meeting_link}
                                    </a>
                                ) : (
                                    <p className="mt-1 font-medium">—</p>
                                )}
                            </div>

                            <Separator />

                            <div>
                                <span className="text-xs text-muted-foreground">
                                    Durasi
                                </span>
                                <p className="mt-1 font-medium">
                                    {schedule.duration_minutes !== null
                                        ? `${schedule.duration_minutes} menit`
                                        : '—'}
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}
