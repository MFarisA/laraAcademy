import { Head, Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';

import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { index } from '@/routes/branches';

type Branch = {
    id: number;
    name: string;
    code: string | null;
    city: string | null;
    address: string | null;
};

type PageProps = {
    branch: {
        data: Branch;
    };
};

export default function Show({ branch: { data: branch } }: PageProps) {
    return (
        <>
            <Head title={`Detail Cabang #${branch.id}`} />

            <h1 className="sr-only">Detail Cabang</h1>

            <div className="space-y-6 px-6 py-6">
                {/* Header & Back Button */}
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <Heading
                        title="Detail Cabang"
                        description={`Melihat informasi lengkap untuk cabang ${branch.name}.`}
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
                                Informasi Cabang
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div>
                                <span className="text-xs text-muted-foreground">
                                    Nama
                                </span>
                                <p className="mt-1 font-medium">
                                    {branch.name}
                                </p>
                            </div>

                            <Separator />

                            <div>
                                <span className="text-xs text-muted-foreground">
                                    Kode
                                </span>
                                <div className="mt-1">
                                    <Badge variant="secondary">
                                        {branch.code ?? '—'}
                                    </Badge>
                                </div>
                            </div>

                            <Separator />

                            <div>
                                <span className="text-xs text-muted-foreground">
                                    Kota
                                </span>
                                <p className="mt-1 font-medium">
                                    {branch.city ?? '—'}
                                </p>
                            </div>

                            <Separator />

                            <div>
                                <span className="text-xs text-muted-foreground">
                                    Alamat
                                </span>
                                <p className="mt-1 leading-relaxed whitespace-pre-wrap">
                                    {branch.address ?? '—'}
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}
