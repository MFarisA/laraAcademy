import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, Pencil } from 'lucide-react';

import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { edit, index } from '@/routes/exam-templates';

type ExamTemplateSection = {
    id: number;
    title: string;
    passing_grade?: number | null;
    duration_minutes?: number | null;
    order_index?: number | null;
};

type ExamTemplate = {
    id: number;
    title: string;
    type: string | null;
    total_duration_minutes: number | null;
    program?: {
        id: number;
        name: string;
    } | null;
    sections?: ExamTemplateSection[];
};

type PageProps = {
    template: {
        data: ExamTemplate;
    };
};

export default function Show({ template: { data: examTemplate } }: PageProps) {
    const sections = examTemplate.sections ?? [];

    const formatDuration = (value?: number | null): string => {
        if (value === null || value === undefined) {
            return '—';
        }

        return `${value} menit`;
    };

    return (
        <>
            <Head title={`Detail Template ${examTemplate.title}`} />

            <h1 className="sr-only">Detail Template Ujian</h1>

            <div className="space-y-6 px-6 py-6">
                {/* Header & Actions */}
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <Heading
                        title="Detail Template Ujian"
                        description={`Melihat informasi dan bagian ujian untuk "${examTemplate.title}".`}
                    />
                    <div className="flex items-center gap-2">
                        <Button variant="outline" size="sm" asChild>
                            <Link href={index.url()}>
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Kembali ke Daftar
                            </Link>
                        </Button>
                        <Button size="sm" asChild>
                            <Link href={edit.url(examTemplate.id)}>
                                <Pencil className="mr-2 h-4 w-4" />
                                Edit Template
                            </Link>
                        </Button>
                    </div>
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    {/* Daftar Bagian Ujian (2 Kolom di Desktop) */}
                    <div className="space-y-6 lg:col-span-2">
                        <Card>
                            <CardHeader>
                                <CardTitle className="text-base font-semibold text-muted-foreground">
                                    Bagian Ujian
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-3">
                                {sections.length === 0 ? (
                                    <p className="text-sm text-muted-foreground">
                                        Belum ada bagian ujian.
                                    </p>
                                ) : (
                                    sections.map((section) => {
                                        return (
                                            <div
                                                key={section.id}
                                                className="flex items-start justify-between gap-4 rounded-lg border border-border bg-card p-4"
                                            >
                                                <div className="flex items-start gap-3">
                                                    <span className="flex h-7 w-7 shrink-0 items-center justify-center rounded-full border border-border bg-muted text-sm font-semibold">
                                                        {section.order_index ??
                                                            '-'}
                                                    </span>

                                                    <div>
                                                        <h4 className="font-semibold text-foreground">
                                                            {section.title}
                                                        </h4>
                                                        <p className="mt-1 text-sm text-muted-foreground">
                                                            Durasi:{' '}
                                                            {formatDuration(
                                                                section.duration_minutes,
                                                            )}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div className="text-right">
                                                    <span className="text-xs text-muted-foreground">
                                                        Passing Grade
                                                    </span>
                                                    <p className="text-lg font-bold text-foreground">
                                                        {section.passing_grade ??
                                                            '—'}
                                                    </p>
                                                </div>
                                            </div>
                                        );
                                    })
                                )}
                            </CardContent>
                        </Card>
                    </div>

                    {/* Informasi Utama Template (1 Kolom di Desktop) */}
                    <div>
                        <Card>
                            <CardHeader>
                                <CardTitle className="text-base font-semibold text-muted-foreground">
                                    Informasi Template
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div>
                                    <span className="text-xs text-muted-foreground">
                                        Judul
                                    </span>
                                    <p className="mt-1 font-medium text-foreground">
                                        {examTemplate.title}
                                    </p>
                                </div>

                                <Separator />

                                <div>
                                    <span className="text-xs text-muted-foreground">
                                        Program
                                    </span>
                                    <div className="mt-1">
                                        <Badge variant="secondary">
                                            {examTemplate.program?.name ?? '—'}
                                        </Badge>
                                    </div>
                                </div>

                                <Separator />

                                <div>
                                    <span className="text-xs text-muted-foreground">
                                        Jenis
                                    </span>
                                    <p className="mt-1 font-medium text-foreground">
                                        {examTemplate.type ?? '—'}
                                    </p>
                                </div>

                                <Separator />

                                <div>
                                    <span className="text-xs text-muted-foreground">
                                        Total Durasi
                                    </span>
                                    <p className="mt-1 font-medium text-foreground">
                                        {formatDuration(
                                            examTemplate.total_duration_minutes,
                                        )}
                                    </p>
                                </div>

                                <Separator />

                                <div>
                                    <span className="text-xs text-muted-foreground">
                                        Total Bagian Ujian
                                    </span>
                                    <p className="mt-1 font-medium text-foreground">
                                        {sections.length} bagian
                                    </p>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </>
    );
}
