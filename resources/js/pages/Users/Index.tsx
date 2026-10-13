import { Head, Link, router } from '@inertiajs/react';
import { MoreVertical, Plus } from 'lucide-react';

import Heading from '@/components/heading';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { create, destroy, edit, index } from '@/routes/users';

type UserBranch = {
    id: number;
    name: string;
};

type User = {
    id: number;
    name: string;
    email: string;
    is_active: boolean;
    branch_id: number | null;
    branch?: UserBranch | null;
    roles?: { id: number; name: string }[];
};

type PaginationMeta = {
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
};

type PageProps = {
    users: {
        data: User[];
        meta: PaginationMeta;
    };
    filters: {
        search: string | null;
        branch_id: string | null;
        is_active: string | null;
        roles: string | null;
    };
};

export default function Index({ users }: PageProps) {
    const { data, meta } = users;

    /**
     * `mergeQuery` dipakai supaya filter lain tetap ada di URL. Kalau pakai
     * `query`, seluruh query string lama hilang setiap kali pindah halaman.
     */
    const goToPage = (page: number) => {
        router.get(
            index.url({ mergeQuery: { page } }),
            {},
            { preserveScroll: true },
        );
    };

    return (
        <>
            <Head title="Pengguna" />

            <h1 className="sr-only">Pengguna</h1>

            <div className="space-y-6 px-6 py-6">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <Heading
                        title="Pengguna"
                        description="Kelola akun pengguna, status aktif, dan penugasan cabangnya."
                    />
                    <Button asChild className="self-start">
                        <Link href={create.url()}>
                            <Plus />
                            Tambah Pengguna
                        </Link>
                    </Button>
                </div>

                <div className="overflow-hidden rounded-lg border border-border bg-card">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">No</TableHead>
                                <TableHead className="w-48">Nama</TableHead>
                                <TableHead>Email</TableHead>
                                <TableHead className="w-40">Cabang</TableHead>
                                <TableHead className="w-28">Status</TableHead>
                                <TableHead className="w-40">Role</TableHead>
                                <TableHead className="w-24 text-right">
                                    Aksi
                                </TableHead>
                            </TableRow>
                        </TableHeader>

                        <TableBody>
                            {data.length === 0 ? (
                                <TableRow>
                                    <TableCell
                                        colSpan={7}
                                        className="h-32 text-center text-muted-foreground"
                                    >
                                        Belum ada pengguna.
                                    </TableCell>
                                </TableRow>
                            ) : (
                                data.map((user, position) => {
                                    return (
                                        <TableRow
                                            key={user.id}
                                            className="hover:bg-muted/40"
                                        >
                                            <TableCell className="text-muted-foreground">
                                                {(meta.from ?? 1) + position}
                                            </TableCell>

                                            <TableCell className="font-medium">
                                                {user.name}
                                            </TableCell>

                                            <TableCell className="text-muted-foreground">
                                                {user.email}
                                            </TableCell>

                                            <TableCell>
                                                <Badge variant="secondary">
                                                    {user.branch?.name ?? '—'}
                                                </Badge>
                                            </TableCell>

                                            <TableCell>
                                                {user.is_active ? (
                                                    <Badge variant="default">
                                                        Aktif
                                                    </Badge>
                                                ) : (
                                                    <Badge variant="outline">
                                                        Nonaktif
                                                    </Badge>
                                                )}
                                            </TableCell>

                                            <TableCell>
                                                <div className="flex flex-wrap gap-1">
                                                    {user.roles &&
                                                    user.roles.length > 0 ? (
                                                        user.roles.map(
                                                            (role) => (
                                                                <Badge
                                                                    key={
                                                                        role.id
                                                                    }
                                                                    variant="secondary"
                                                                >
                                                                    {role.name}
                                                                </Badge>
                                                            ),
                                                        )
                                                    ) : (
                                                        <span className="text-muted-foreground">
                                                            —
                                                        </span>
                                                    )}
                                                </div>
                                            </TableCell>

                                            <TableCell className="text-right">
                                                <DropdownMenu>
                                                    <DropdownMenuTrigger
                                                        asChild
                                                    >
                                                        <Button
                                                            variant="outline"
                                                            size="icon-sm"
                                                            aria-label="Aksi pengguna"
                                                        >
                                                            <MoreVertical />
                                                        </Button>
                                                    </DropdownMenuTrigger>
                                                    <DropdownMenuContent align="end">
                                                        <DropdownMenuItem
                                                            asChild
                                                        >
                                                            <Link
                                                                href={edit.url(
                                                                    user.id,
                                                                )}
                                                            >
                                                                Edit
                                                            </Link>
                                                        </DropdownMenuItem>
                                                        <DropdownMenuSeparator />
                                                        <AlertDialog>
                                                            <AlertDialogTrigger
                                                                asChild
                                                            >
                                                                <DropdownMenuItem
                                                                    variant="destructive"
                                                                    onSelect={(
                                                                        event,
                                                                    ) =>
                                                                        event.preventDefault()
                                                                    }
                                                                >
                                                                    Hapus
                                                                </DropdownMenuItem>
                                                            </AlertDialogTrigger>
                                                            <AlertDialogContent>
                                                                <AlertDialogHeader>
                                                                    <AlertDialogTitle>
                                                                        Hapus
                                                                        pengguna
                                                                        ini?
                                                                    </AlertDialogTitle>
                                                                    <AlertDialogDescription>
                                                                        Tindakan
                                                                        ini
                                                                        tidak
                                                                        bisa
                                                                        dibatalkan.
                                                                    </AlertDialogDescription>
                                                                </AlertDialogHeader>
                                                                <AlertDialogFooter>
                                                                    <AlertDialogCancel>
                                                                        Batal
                                                                    </AlertDialogCancel>
                                                                    <AlertDialogAction
                                                                        variant="destructive"
                                                                        asChild
                                                                    >
                                                                        <Link
                                                                            href={destroy.url(
                                                                                user.id,
                                                                            )}
                                                                            method="delete"
                                                                            as="button"
                                                                        >
                                                                            Hapus
                                                                        </Link>
                                                                    </AlertDialogAction>
                                                                </AlertDialogFooter>
                                                            </AlertDialogContent>
                                                        </AlertDialog>
                                                    </DropdownMenuContent>
                                                </DropdownMenu>
                                            </TableCell>
                                        </TableRow>
                                    );
                                })
                            )}
                        </TableBody>
                    </Table>
                </div>

                {data.length > 0 && (
                    <div className="flex flex-wrap items-center justify-between gap-4">
                        <p className="text-sm text-muted-foreground">
                            Menampilkan {meta.from ?? 0}–{meta.to ?? 0} dari{' '}
                            {meta.total} pengguna
                        </p>

                        <div className="flex items-center gap-2">
                            <span className="text-sm text-muted-foreground">
                                Halaman {meta.current_page} dari{' '}
                                {meta.last_page}
                            </span>

                            <Button
                                variant="outline"
                                size="sm"
                                disabled={meta.current_page <= 1}
                                onClick={() => goToPage(meta.current_page - 1)}
                            >
                                Sebelumnya
                            </Button>

                            <Button
                                variant="outline"
                                size="sm"
                                disabled={meta.current_page >= meta.last_page}
                                onClick={() => goToPage(meta.current_page + 1)}
                            >
                                Selanjutnya
                            </Button>
                        </div>
                    </div>
                )}
            </div>
        </>
    );
}
