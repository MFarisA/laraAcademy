import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Plus, Save, Trash2 } from 'lucide-react';
import type { FormEvent } from 'react';

import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import { index, store } from '@/routes/exam-templates';

type OptionItem = {
    id: number;
    name: string;
};

type FormSection = {
    subject_id: string;
    title: string;
    passing_grade: string;
    duration_minutes: string;
    order_index: number;
};

type PageProps = {
    programs: OptionItem[];
    subjects: OptionItem[];
};

export default function Create({ programs = [], subjects = [] }: PageProps) {
    const form = useForm({
        title: '',
        program_id: '',
        type: 'skd',
        total_duration_minutes: '100',
        sections: [
            {
                subject_id: '',
                title: '',
                passing_grade: '65',
                duration_minutes: '30',
                order_index: 1,
            },
        ] as FormSection[],
    });

    const handleAddSection = () => {
        form.setData('sections', [
            ...form.data.sections,
            {
                subject_id: '',
                title: '',
                passing_grade: '65',
                duration_minutes: '30',
                order_index: form.data.sections.length + 1,
            },
        ]);
    };

    const handleRemoveSection = (indexToRemove: number) => {
        const next = form.data.sections
            .filter((_, i) => i !== indexToRemove)
            .map((sec, i) => ({ ...sec, order_index: i + 1 }));

        form.setData('sections', next);
    };

    const handleSectionChange = <K extends keyof FormSection>(
        indexToUpdate: number,
        field: K,
        val: FormSection[K],
    ) => {
        const next = [...form.data.sections];

        next[indexToUpdate] = {
            ...next[indexToUpdate],
            [field]: val,
        };
        form.setData('sections', next);
    };

    const handleSubmit = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();

        const payload = {
            title: form.data.title,
            program_id: Number(form.data.program_id),
            type: form.data.type,
            total_duration_minutes: Number(form.data.total_duration_minutes),
            sections: form.data.sections.map((s, i) => ({
                subject_id: Number(s.subject_id),
                title: s.title,
                passing_grade: Number(s.passing_grade),
                duration_minutes: Number(s.duration_minutes),
                order_index: i + 1,
            })),
        };

        form.transform(() => payload);
        form.post(store.url());
    };

    return (
        <>
            <Head title="Tambah Template Ujian" />

            <h1 className="sr-only">Tambah Template Ujian</h1>

            <div className="space-y-6 px-6 py-6">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <Heading
                        title="Tambah Template Ujian"
                        description="Konfigurasikan template ujian baru beserta subtes/bagian ujiannya."
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

                <form onSubmit={handleSubmit} className="space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle className="text-base">
                                Informasi Dasar Template
                            </CardTitle>
                            <CardDescription>
                                Masukkan judul, jenis ujian, program akademik,
                                dan total durasi waktu pengerjaan.
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="space-y-2">
                                <Label htmlFor="title">
                                    Judul Template{' '}
                                    <span className="text-destructive">*</span>
                                </Label>
                                <Input
                                    id="title"
                                    placeholder="Contoh: Tryout Akbar SKD CPNS 2026"
                                    value={form.data.title}
                                    onChange={(e) =>
                                        form.setData('title', e.target.value)
                                    }
                                    required
                                />
                                <InputError message={form.errors.title} />
                            </div>

                            <div className="grid gap-4 sm:grid-cols-3">
                                <div className="space-y-2">
                                    <Label htmlFor="program_id">
                                        Program Akademik{' '}
                                        <span className="text-destructive">
                                            *
                                        </span>
                                    </Label>
                                    <Select
                                        value={form.data.program_id}
                                        onValueChange={(val) =>
                                            form.setData('program_id', val)
                                        }
                                    >
                                        <SelectTrigger id="program_id">
                                            <SelectValue placeholder="Pilih program" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {programs.map((p) => (
                                                <SelectItem
                                                    key={p.id}
                                                    value={String(p.id)}
                                                >
                                                    {p.name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    <InputError
                                        message={form.errors.program_id}
                                    />
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="type">
                                        Jenis Template{' '}
                                        <span className="text-destructive">
                                            *
                                        </span>
                                    </Label>
                                    <Select
                                        value={form.data.type}
                                        onValueChange={(val) =>
                                            form.setData('type', val)
                                        }
                                    >
                                        <SelectTrigger id="type">
                                            <SelectValue placeholder="Pilih jenis" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="skd">
                                                SKD
                                            </SelectItem>
                                            <SelectItem value="snbt">
                                                SNBT
                                            </SelectItem>
                                            <SelectItem value="kedinasan">
                                                Kedinasan
                                            </SelectItem>
                                            <SelectItem value="simulation">
                                                Simulasi
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <InputError message={form.errors.type} />
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="total_duration_minutes">
                                        Total Durasi (Menit){' '}
                                        <span className="text-destructive">
                                            *
                                        </span>
                                    </Label>
                                    <Input
                                        id="total_duration_minutes"
                                        type="number"
                                        min={1}
                                        max={1440}
                                        value={form.data.total_duration_minutes}
                                        onChange={(e) =>
                                            form.setData(
                                                'total_duration_minutes',
                                                e.target.value,
                                            )
                                        }
                                        required
                                    />
                                    <InputError
                                        message={
                                            form.errors.total_duration_minutes
                                        }
                                    />
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between">
                            <div>
                                <CardTitle className="text-base">
                                    Bagian / Subtes Ujian
                                </CardTitle>
                                <CardDescription>
                                    Tentukan subtes (seperti TWK, TIU, TKP)
                                    beserta nilai ambang batas (passing grade).
                                </CardDescription>
                            </div>
                            <Button
                                type="button"
                                variant="outline"
                                size="sm"
                                onClick={handleAddSection}
                            >
                                <Plus className="mr-1.5 h-4 w-4" />
                                Tambah Subtes
                            </Button>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            {form.data.sections.length === 0 ? (
                                <p className="text-sm text-muted-foreground">
                                    Belum ada subtes ditambahkan. Klik tombol di
                                    atas untuk menambahkan subtes.
                                </p>
                            ) : (
                                form.data.sections.map((section, idx) => (
                                    <div
                                        key={idx}
                                        className="relative space-y-4 rounded-lg border border-border bg-muted/20 p-4"
                                    >
                                        <div className="flex items-center justify-between">
                                            <span className="text-sm font-semibold">
                                                Subtes #{idx + 1}
                                            </span>
                                            {form.data.sections.length > 1 && (
                                                <Button
                                                    type="button"
                                                    variant="ghost"
                                                    size="sm"
                                                    className="h-8 px-2 text-destructive hover:bg-destructive/10"
                                                    onClick={() =>
                                                        handleRemoveSection(idx)
                                                    }
                                                >
                                                    <Trash2 className="mr-1 h-4 w-4" />
                                                    Hapus
                                                </Button>
                                            )}
                                        </div>

                                        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                            <div className="space-y-2">
                                                <Label
                                                    htmlFor={`sec_title_${idx}`}
                                                >
                                                    Judul Subtes{' '}
                                                    <span className="text-destructive">
                                                        *
                                                    </span>
                                                </Label>
                                                <Input
                                                    id={`sec_title_${idx}`}
                                                    placeholder="Contoh: Tes Wawasan Kebangsaan"
                                                    value={section.title}
                                                    onChange={(e) =>
                                                        handleSectionChange(
                                                            idx,
                                                            'title',
                                                            e.target.value,
                                                        )
                                                    }
                                                    required
                                                />
                                            </div>

                                            <div className="space-y-2">
                                                <Label
                                                    htmlFor={`sec_subject_${idx}`}
                                                >
                                                    Mata Pelajaran{' '}
                                                    <span className="text-destructive">
                                                        *
                                                    </span>
                                                </Label>
                                                <Select
                                                    value={section.subject_id}
                                                    onValueChange={(val) =>
                                                        handleSectionChange(
                                                            idx,
                                                            'subject_id',
                                                            val,
                                                        )
                                                    }
                                                >
                                                    <SelectTrigger
                                                        id={`sec_subject_${idx}`}
                                                    >
                                                        <SelectValue placeholder="Pilih mapel" />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        {subjects.map((sub) => (
                                                            <SelectItem
                                                                key={sub.id}
                                                                value={String(
                                                                    sub.id,
                                                                )}
                                                            >
                                                                {sub.name}
                                                            </SelectItem>
                                                        ))}
                                                    </SelectContent>
                                                </Select>
                                            </div>

                                            <div className="space-y-2">
                                                <Label
                                                    htmlFor={`sec_grade_${idx}`}
                                                >
                                                    Passing Grade{' '}
                                                    <span className="text-destructive">
                                                        *
                                                    </span>
                                                </Label>
                                                <Input
                                                    id={`sec_grade_${idx}`}
                                                    type="number"
                                                    step="any"
                                                    min={0}
                                                    placeholder="Contoh: 65"
                                                    value={
                                                        section.passing_grade
                                                    }
                                                    onChange={(e) =>
                                                        handleSectionChange(
                                                            idx,
                                                            'passing_grade',
                                                            e.target.value,
                                                        )
                                                    }
                                                    required
                                                />
                                            </div>

                                            <div className="space-y-2">
                                                <Label
                                                    htmlFor={`sec_dur_${idx}`}
                                                >
                                                    Durasi (Menit){' '}
                                                    <span className="text-destructive">
                                                        *
                                                    </span>
                                                </Label>
                                                <Input
                                                    id={`sec_dur_${idx}`}
                                                    type="number"
                                                    min={1}
                                                    placeholder="Contoh: 30"
                                                    value={
                                                        section.duration_minutes
                                                    }
                                                    onChange={(e) =>
                                                        handleSectionChange(
                                                            idx,
                                                            'duration_minutes',
                                                            e.target.value,
                                                        )
                                                    }
                                                    required
                                                />
                                            </div>
                                        </div>
                                    </div>
                                ))
                            )}
                        </CardContent>
                    </Card>

                    <div className="flex justify-end gap-3">
                        <Button type="button" variant="outline" asChild>
                            <Link href={index.url()}>Batal</Link>
                        </Button>
                        <Button
                            type="submit"
                            disabled={form.processing}
                            className="gap-2"
                        >
                            {form.processing ? (
                                <Spinner className="h-4 w-4" />
                            ) : (
                                <Save className="h-4 w-4" />
                            )}
                            Simpan Template
                        </Button>
                    </div>
                </form>
            </div>
        </>
    );
}
