import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Save } from 'lucide-react';
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
import { index, store } from '@/routes/exam-sessions';

type OptionItem = {
    id: number;
    title?: string;
    name?: string;
};

type PageProps = {
    templates: OptionItem[];
    classrooms: OptionItem[];
};

export default function Create({ templates = [], classrooms = [] }: PageProps) {
    const form = useForm({
        exam_template_id: '',
        classroom_id: 'all',
        title: '',
        token: '',
        start_time: '',
        end_time: '',
    });

    const handleSubmit = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();

        const payload = {
            exam_template_id: Number(form.data.exam_template_id),
            classroom_id:
                form.data.classroom_id === 'all'
                    ? null
                    : Number(form.data.classroom_id),
            title: form.data.title,
            token: form.data.token
                ? form.data.token.toUpperCase().trim()
                : null,
            start_time: form.data.start_time,
            end_time: form.data.end_time,
        };

        form.transform(() => payload);
        form.post(store.url());
    };

    return (
        <>
            <Head title="Tambah Sesi Ujian" />

            <h1 className="sr-only">Tambah Sesi Ujian</h1>

            <div className="space-y-6 px-6 py-6">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <Heading
                        title="Tambah Sesi Ujian"
                        description="Jadwalkan sesi ujian baru untuk kelas tertentu atau terbuka secara publik."
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
                                Informasi Sesi Ujian
                            </CardTitle>
                            <CardDescription>
                                Pilih template ujian yang digunakan, target
                                peserta kelas, dan jendela waktu pelaksanaan.
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="space-y-2">
                                <Label htmlFor="title">
                                    Judul Sesi{' '}
                                    <span className="text-destructive">*</span>
                                </Label>
                                <Input
                                    id="title"
                                    placeholder="Contoh: Sesi Pagi - Tryout Akbar CPNS"
                                    maxLength={50}
                                    value={form.data.title}
                                    onChange={(e) =>
                                        form.setData('title', e.target.value)
                                    }
                                    required
                                />
                                <InputError message={form.errors.title} />
                            </div>

                            <div className="grid gap-4 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="exam_template_id">
                                        Template Ujian{' '}
                                        <span className="text-destructive">
                                            *
                                        </span>
                                    </Label>
                                    <Select
                                        value={form.data.exam_template_id}
                                        onValueChange={(val) =>
                                            form.setData(
                                                'exam_template_id',
                                                val,
                                            )
                                        }
                                    >
                                        <SelectTrigger id="exam_template_id">
                                            <SelectValue placeholder="Pilih template ujian" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {templates.map((tpl) => (
                                                <SelectItem
                                                    key={tpl.id}
                                                    value={String(tpl.id)}
                                                >
                                                    {tpl.title ?? tpl.name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    <InputError
                                        message={form.errors.exam_template_id}
                                    />
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="classroom_id">
                                        Target Kelas
                                    </Label>
                                    <Select
                                        value={form.data.classroom_id}
                                        onValueChange={(val) =>
                                            form.setData('classroom_id', val)
                                        }
                                    >
                                        <SelectTrigger id="classroom_id">
                                            <SelectValue placeholder="Pilih kelas atau publik" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="all">
                                                Terbuka untuk Semua Kelas
                                                (Publik)
                                            </SelectItem>
                                            {classrooms.map((cls) => (
                                                <SelectItem
                                                    key={cls.id}
                                                    value={String(cls.id)}
                                                >
                                                    {cls.name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    <InputError
                                        message={form.errors.classroom_id}
                                    />
                                </div>
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="token">
                                    Token Ujian (Opsional)
                                </Label>
                                <Input
                                    id="token"
                                    placeholder="Biarkan kosong untuk generate token otomatis (maks 10 karakter)"
                                    maxLength={10}
                                    value={form.data.token}
                                    onChange={(e) =>
                                        form.setData(
                                            'token',
                                            e.target.value.toUpperCase(),
                                        )
                                    }
                                    className="font-mono tracking-wider uppercase"
                                />
                                <p className="text-xs text-muted-foreground">
                                    Jika dikosongkan, sistem akan mengenerate
                                    token acak 6 karakter unik.
                                </p>
                                <InputError message={form.errors.token} />
                            </div>

                            <div className="grid gap-4 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="start_time">
                                        Waktu Mulai{' '}
                                        <span className="text-destructive">
                                            *
                                        </span>
                                    </Label>
                                    <Input
                                        id="start_time"
                                        type="datetime-local"
                                        value={form.data.start_time}
                                        onChange={(e) =>
                                            form.setData(
                                                'start_time',
                                                e.target.value,
                                            )
                                        }
                                        required
                                    />
                                    <InputError
                                        message={form.errors.start_time}
                                    />
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="end_time">
                                        Waktu Berakhir{' '}
                                        <span className="text-destructive">
                                            *
                                        </span>
                                    </Label>
                                    <Input
                                        id="end_time"
                                        type="datetime-local"
                                        value={form.data.end_time}
                                        onChange={(e) =>
                                            form.setData(
                                                'end_time',
                                                e.target.value,
                                            )
                                        }
                                        required
                                    />
                                    <InputError
                                        message={form.errors.end_time}
                                    />
                                </div>
                            </div>
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
                            Simpan Sesi Ujian
                        </Button>
                    </div>
                </form>
            </div>
        </>
    );
}
