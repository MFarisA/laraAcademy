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
import { index, store } from '@/routes/schedules';

type OptionItem = {
    id: number;
    name: string;
};

type PageProps = {
    classrooms: OptionItem[];
    subjects: OptionItem[];
    instructors: OptionItem[];
};

export default function Create({
    classrooms = [],
    subjects = [],
    instructors = [],
}: PageProps) {
    const form = useForm({
        classroom_id: '',
        subject_id: '',
        instructor_id: '',
        room_type: 'offline',
        meeting_link: '',
        scheduled_at: '',
        duration_minutes: '90',
    });

    const handleSubmit = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        form.post(store.url());
    };

    return (
        <>
            <Head title="Tambah Jadwal Kelas" />

            <h1 className="sr-only">Tambah Jadwal Kelas</h1>

            <div className="space-y-6 px-6 py-6">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <Heading
                        title="Tambah Jadwal Kelas"
                        description="Atur jadwal sesi tatap muka atau kelas daring bersama pengajar."
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
                                Informasi Jadwal
                            </CardTitle>
                            <CardDescription>
                                Pilih kelas, mata pelajaran, pengajar, serta
                                waktu dan jenis ruang belajar.
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="grid gap-4 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="classroom_id">
                                        Kelas{' '}
                                        <span className="text-destructive">
                                            *
                                        </span>
                                    </Label>
                                    <Select
                                        value={form.data.classroom_id}
                                        onValueChange={(val) =>
                                            form.setData('classroom_id', val)
                                        }
                                    >
                                        <SelectTrigger id="classroom_id">
                                            <SelectValue placeholder="Pilih kelas" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {classrooms.map((c) => (
                                                <SelectItem
                                                    key={c.id}
                                                    value={String(c.id)}
                                                >
                                                    {c.name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    <InputError
                                        message={form.errors.classroom_id}
                                    />
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="subject_id">
                                        Mata Pelajaran{' '}
                                        <span className="text-destructive">
                                            *
                                        </span>
                                    </Label>
                                    <Select
                                        value={form.data.subject_id}
                                        onValueChange={(val) =>
                                            form.setData('subject_id', val)
                                        }
                                    >
                                        <SelectTrigger id="subject_id">
                                            <SelectValue placeholder="Pilih mata pelajaran" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {subjects.map((s) => (
                                                <SelectItem
                                                    key={s.id}
                                                    value={String(s.id)}
                                                >
                                                    {s.name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    <InputError
                                        message={form.errors.subject_id}
                                    />
                                </div>
                            </div>

                            <div className="grid gap-4 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="instructor_id">
                                        Pengajar / Instruktur{' '}
                                        <span className="text-destructive">
                                            *
                                        </span>
                                    </Label>
                                    <Select
                                        value={form.data.instructor_id}
                                        onValueChange={(val) =>
                                            form.setData('instructor_id', val)
                                        }
                                    >
                                        <SelectTrigger id="instructor_id">
                                            <SelectValue placeholder="Pilih pengajar" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {instructors.map((i) => (
                                                <SelectItem
                                                    key={i.id}
                                                    value={String(i.id)}
                                                >
                                                    {i.name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    <InputError
                                        message={form.errors.instructor_id}
                                    />
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="room_type">
                                        Tipe Ruang{' '}
                                        <span className="text-destructive">
                                            *
                                        </span>
                                    </Label>
                                    <Select
                                        value={form.data.room_type}
                                        onValueChange={(val) =>
                                            form.setData('room_type', val)
                                        }
                                    >
                                        <SelectTrigger id="room_type">
                                            <SelectValue placeholder="Pilih tipe ruang" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="offline">
                                                Tatap Muka (Offline)
                                            </SelectItem>
                                            <SelectItem value="online">
                                                Daring (Online)
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <InputError
                                        message={form.errors.room_type}
                                    />
                                </div>
                            </div>

                            {form.data.room_type === 'online' && (
                                <div className="space-y-2">
                                    <Label htmlFor="meeting_link">
                                        Tautan Pertemuan Online (Zoom/Google
                                        Meet){' '}
                                        <span className="text-destructive">
                                            *
                                        </span>
                                    </Label>
                                    <Input
                                        id="meeting_link"
                                        placeholder="https://meet.google.com/..."
                                        value={form.data.meeting_link}
                                        onChange={(e) =>
                                            form.setData(
                                                'meeting_link',
                                                e.target.value,
                                            )
                                        }
                                        required
                                    />
                                    <InputError
                                        message={form.errors.meeting_link}
                                    />
                                </div>
                            )}

                            <div className="grid gap-4 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="scheduled_at">
                                        Waktu Jadwal{' '}
                                        <span className="text-destructive">
                                            *
                                        </span>
                                    </Label>
                                    <Input
                                        id="scheduled_at"
                                        type="datetime-local"
                                        value={form.data.scheduled_at}
                                        onChange={(e) =>
                                            form.setData(
                                                'scheduled_at',
                                                e.target.value,
                                            )
                                        }
                                        required
                                    />
                                    <InputError
                                        message={form.errors.scheduled_at}
                                    />
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="duration_minutes">
                                        Durasi (Menit){' '}
                                        <span className="text-destructive">
                                            *
                                        </span>
                                    </Label>
                                    <Input
                                        id="duration_minutes"
                                        type="number"
                                        min={15}
                                        max={480}
                                        value={form.data.duration_minutes}
                                        onChange={(e) =>
                                            form.setData(
                                                'duration_minutes',
                                                e.target.value,
                                            )
                                        }
                                        required
                                    />
                                    <InputError
                                        message={form.errors.duration_minutes}
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
                            Simpan Jadwal
                        </Button>
                    </div>
                </form>
            </div>
        </>
    );
}
