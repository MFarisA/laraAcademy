import { Head } from '@inertiajs/react';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

type AttendanceSummary = {
    student_id: number;
    student_name: string;
    total_sessions: number;
    present_count: number;
    absent_count: number;
    sick_count: number;
    permitted_count: number;
    attendance_percentage: number;
};

type PageProps = {
    classroom: {
        id: number;
        name: string;
    };
    summaries: {
        data: AttendanceSummary[];
    };
};

export default function Summary({ classroom, summaries }: PageProps) {
    const { data } = summaries;

    return (
        <>
            <Head title="Rekap Kehadiran" />

            <h1 className="sr-only">Rekap Kehadiran</h1>

            <div className="space-y-6 px-6 py-6">
                <Heading
                    title="Rekap Kehadiran"
                    description={`Rekapitulasi kehadiran siswa pada kelas ${classroom.name}.`}
                />

                <div className="overflow-hidden rounded-lg border border-border bg-card">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">No</TableHead>
                                <TableHead className="w-56">
                                    Nama Siswa
                                </TableHead>
                                <TableHead className="w-24">
                                    Total Sesi
                                </TableHead>
                                <TableHead className="w-20">Hadir</TableHead>
                                <TableHead className="w-20">Sakit</TableHead>
                                <TableHead className="w-20">Izin</TableHead>
                                <TableHead className="w-20">Alfa</TableHead>
                                <TableHead className="w-28">
                                    Kehadiran
                                </TableHead>
                            </TableRow>
                        </TableHeader>

                        <TableBody>
                            {data.length === 0 ? (
                                <TableRow>
                                    <TableCell
                                        colSpan={8}
                                        className="h-32 text-center text-muted-foreground"
                                    >
                                        Belum ada data kehadiran untuk kelas
                                        ini.
                                    </TableCell>
                                </TableRow>
                            ) : (
                                data.map((summary, position) => {
                                    return (
                                        <TableRow
                                            key={summary.student_id}
                                            className="hover:bg-muted/40"
                                        >
                                            <TableCell className="text-muted-foreground">
                                                {position + 1}
                                            </TableCell>

                                            <TableCell>
                                                {summary.student_name}
                                            </TableCell>

                                            <TableCell>
                                                {summary.total_sessions}
                                            </TableCell>

                                            <TableCell>
                                                {summary.present_count}
                                            </TableCell>

                                            <TableCell>
                                                {summary.sick_count}
                                            </TableCell>

                                            <TableCell>
                                                {summary.permitted_count}
                                            </TableCell>

                                            <TableCell>
                                                {summary.absent_count}
                                            </TableCell>

                                            <TableCell>
                                                <Badge variant="secondary">
                                                    {
                                                        summary.attendance_percentage
                                                    }
                                                    %
                                                </Badge>
                                            </TableCell>
                                        </TableRow>
                                    );
                                })
                            )}
                        </TableBody>
                    </Table>
                </div>
            </div>
        </>
    );
}
