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
import { Checkbox } from '@/components/ui/checkbox';
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
import { index, update } from '@/routes/users';

type UserData = {
    id: number;
    name: string;
    email: string;
    is_active: boolean;
    branch_id: number | null;
    roles?: { id: number; name: string }[];
};

type OptionItem = {
    id: number;
    name: string;
};

type PageProps = {
    user: {
        data: UserData;
    };
    branches?: OptionItem[];
    roles?: OptionItem[];
};

export default function Edit({
    user: { data: user },
    branches = [],
    roles = [],
}: PageProps) {
    const form = useForm({
        name: user.name,
        email: user.email,
        password: '',
        branch_id: user.branch_id ? String(user.branch_id) : '',
        roles: (user.roles || []).map((r) => r.name),
        is_active: user.is_active,
    });

    const handleSubmit = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        form.put(update.url(user.id));
    };

    const handleRoleToggle = (roleName: string) => {
        const current = form.data.roles;

        if (current.includes(roleName)) {
            form.setData(
                'roles',
                current.filter((r) => r !== roleName),
            );
        } else {
            form.setData('roles', [...current, roleName]);
        }
    };

    return (
        <>
            <Head title={`Edit Pengguna ${user.name}`} />

            <h1 className="sr-only">Edit Pengguna</h1>

            <div className="space-y-6 px-6 py-6">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <Heading
                        title="Edit Pengguna"
                        description={`Perbarui profil, peran, dan status untuk "${user.name}".`}
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
                                Informasi Akun
                            </CardTitle>
                            <CardDescription>
                                Perbarui informasi profil pengguna. Kosongkan
                                password jika tidak ingin mengubahnya.
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="grid gap-4 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="name">
                                        Nama Lengkap{' '}
                                        <span className="text-destructive">
                                            *
                                        </span>
                                    </Label>
                                    <Input
                                        id="name"
                                        placeholder="Nama lengkap pengguna"
                                        value={form.data.name}
                                        onChange={(e) =>
                                            form.setData('name', e.target.value)
                                        }
                                        required
                                    />
                                    <InputError message={form.errors.name} />
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="email">
                                        Alamat Email{' '}
                                        <span className="text-destructive">
                                            *
                                        </span>
                                    </Label>
                                    <Input
                                        id="email"
                                        type="email"
                                        placeholder="user@example.com"
                                        value={form.data.email}
                                        onChange={(e) =>
                                            form.setData(
                                                'email',
                                                e.target.value,
                                            )
                                        }
                                        required
                                    />
                                    <InputError message={form.errors.email} />
                                </div>
                            </div>

                            <div className="grid gap-4 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="password">
                                        Password Baru (Opsional)
                                    </Label>
                                    <Input
                                        id="password"
                                        type="password"
                                        placeholder="Isi hanya jika ingin mengganti password"
                                        value={form.data.password}
                                        onChange={(e) =>
                                            form.setData(
                                                'password',
                                                e.target.value,
                                            )
                                        }
                                    />
                                    <InputError
                                        message={form.errors.password}
                                    />
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="branch_id">Cabang</Label>
                                    <Select
                                        value={
                                            form.data.branch_id || '__none__'
                                        }
                                        onValueChange={(val) =>
                                            form.setData(
                                                'branch_id',
                                                val === '__none__' ? '' : val,
                                            )
                                        }
                                    >
                                        <SelectTrigger id="branch_id">
                                            <SelectValue placeholder="Pilih cabang (opsional)" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="__none__">
                                                Tidak Terikat Cabang (Pusat)
                                            </SelectItem>
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
                            </div>

                            <div className="space-y-3 pt-2">
                                <Label>Peran / Role</Label>
                                <div className="flex flex-wrap gap-4">
                                    {roles.map((r) => {
                                        const isChecked =
                                            form.data.roles.includes(r.name);

                                        return (
                                            <label
                                                key={r.id}
                                                className="flex cursor-pointer items-center gap-2 rounded-lg border border-border p-3 transition-colors hover:bg-muted/50"
                                            >
                                                <Checkbox
                                                    checked={isChecked}
                                                    onCheckedChange={() =>
                                                        handleRoleToggle(r.name)
                                                    }
                                                />
                                                <span className="text-sm font-medium">
                                                    {r.name}
                                                </span>
                                            </label>
                                        );
                                    })}
                                </div>
                                <InputError message={form.errors.roles} />
                            </div>

                            <div className="pt-2">
                                <label className="flex cursor-pointer items-center gap-2">
                                    <Checkbox
                                        checked={form.data.is_active}
                                        onCheckedChange={(checked) =>
                                            form.setData('is_active', !!checked)
                                        }
                                    />
                                    <span className="text-sm font-medium">
                                        Akun Aktif
                                    </span>
                                </label>
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
                            Perbarui Pengguna
                        </Button>
                    </div>
                </form>
            </div>
        </>
    );
}
