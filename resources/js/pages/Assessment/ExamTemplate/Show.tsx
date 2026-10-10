import { Head, Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';

import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { index } from '@/routes/exam-templates';

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
                {/* Header & Back Button */}
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <Heading
                        title="Detail Template Ujian"
                        description={`Melihat informasi dan bagian ujian untuk "${examTemplate.title}".`}
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

                                                    <div className="pt-0.5">
                                                        <p className="font-medium">
                                                            {section.title}
                                                        </p>

                                                        <p className="text-xs text-muted-foreground">
                                                            Passing grade:{' '}
                                                            {section.passing_grade ??
                                                                '—'}
                                                        </p>
                                                    </div>
                                                </div>

                                                <Badge
                                                    variant="outline"
                                                    className="shrink-0"
                                                >
                                                    {formatDuration(
                                                        section.duration_minutes,
                                                    )}
                                                </Badge>
                                            </div>
                                        );
                                    })
                                )}
                            </CardContent>
                        </Card>
                    </div>

                    {/* Informasi Template (1 Kolom Sidebar) */}
                    <div>
                        <Card>
                            <CardHeader>
                                <CardTitle className="text-base font-semibold">
                                    Informasi Template
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div>
                                    <span className="text-xs text-muted-foreground">
                                        Judul
                                    </span>
                                    <p className="mt-1 font-medium">
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
                                    <p className="mt-1 font-medium">
                                        {examTemplate.type ?? '—'}
                                    </p>
                                </div>

                                <Separator />

                                <div>
                                    <span className="text-xs text-muted-foreground">
                                        Durasi Total
                                    </span>
                                    <p className="mt-1 font-medium">
                                        {formatDuration(
                                            examTemplate.total_duration_minutes,
                                        )}
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
