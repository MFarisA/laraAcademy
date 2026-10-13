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
import { index, update } from '@/routes/classrooms';

type OptionItem = {
    id: number;
    name: string;
};

type ClassroomData = {
    id: number;
    batch_id: number;
    branch_id: number;
    name: string;
    capacity: number;
};

type PageProps = {
    classroom: {
        data: ClassroomData;
    };
    batches?: OptionItem[];
    branches?: OptionItem[];
};

export default function Edit({
    classroom: { data: classroom },
    batches = [],
    branches = [],
}: PageProps) {
    const form = useForm({
        batch_id: String(classroom.batch_id),
        branch_id: String(classroom.branch_id),
        name: classroom.name,
        capacity: String(classroom.capacity),
    });

    const handleSubmit = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        form.put(update.url(classroom.id));
    };

    return (
        <>
            <Head title={`Edit Kelas ${classroom.name}`} />

            <h1 className="sr-only">Edit Kelas</h1>

            <div className="space-y-6 px-6 py-6">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <Heading
                        title="Edit Kelas"
                        description={`Perbarui informasi data untuk kelas "${classroom.name}".`}
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
                                Informasi Kelas
                            </CardTitle>
                            <CardDescription>
                                Perbarui cabang, angkatan, nama, dan kapasitas
                                maksimal kelas.
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="grid gap-4 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="branch_id">
                                        Cabang{' '}
                                        <span className="text-destructive">
                                            *
                                        </span>
                                    </Label>
                                    <Select
                                        value={form.data.branch_id}
                                        onValueChange={(val) =>
                                            form.setData('branch_id', val)
                                        }
                                    >
                                        <SelectTrigger id="branch_id">
                                            <SelectValue placeholder="Pilih cabang" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {branches.map((b) => (
                                                <SelectItem
                                                    key={b.id}
                                                    value={String(b.id)}
                                                >
                                                    {b.name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    <InputError
                                        message={form.errors.branch_id}
                                    />
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="batch_id">
                                        Angkatan / Batch{' '}
                                        <span className="text-destructive">
                                            *
                                        </span>
                                    </Label>
                                    <Select
                                        value={form.data.batch_id}
                                        onValueChange={(val) =>
                                            form.setData('batch_id', val)
                                        }
                                    >
                                        <SelectTrigger id="batch_id">
                                            <SelectValue placeholder="Pilih angkatan" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {batches.map((b) => (
                                                <SelectItem
                                                    key={b.id}
                                                    value={String(b.id)}
                                                >
                                                    {b.name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    <InputError
                                        message={form.errors.batch_id}
                                    />
                                </div>
                            </div>

                            <div className="grid gap-4 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="name">
                                        Nama Kelas{' '}
                                        <span className="text-destructive">
                                            *
                                        </span>
                                    </Label>
                                    <Input
                                        id="name"
                                        placeholder="Contoh: Kelas CPNS Eksekutif A"
                                        value={form.data.name}
                                        onChange={(e) =>
                                            form.setData('name', e.target.value)
                                        }
                                        required
                                    />
                                    <InputError message={form.errors.name} />
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="capacity">
                                        Kapasitas Siswa{' '}
                                        <span className="text-destructive">
                                            *
                                        </span>
                                    </Label>
                                    <Input
                                        id="capacity"
                                        type="number"
                                        min={1}
                                        placeholder="30"
                                        value={form.data.capacity}
                                        onChange={(e) =>
                                            form.setData(
                                                'capacity',
                                                e.target.value,
                                            )
                                        }
                                        required
                                    />
                                    <InputError
                                        message={form.errors.capacity}
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
                            Perbarui Kelas
                        </Button>
                    </div>
                </form>
            </div>
        </>
    );
}
