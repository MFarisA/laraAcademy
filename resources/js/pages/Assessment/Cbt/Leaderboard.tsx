import { Head, Link, router } from '@inertiajs/react';
import { ChevronLeft, Crown, MapPin, Medal, Trophy } from 'lucide-react';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { index, leaderboard } from '@/routes/cbt/sessions';

interface StudentInfo {
    id: number;
    name: string;
    branch?: {
        id: number;
        name: string;
    } | null;
}

interface AttemptItem {
    id: number;
    student_id: number;
    total_score: string | number;
    national_rank: number | null;
    branch_rank: number | null;
    is_passed: boolean;
    duration_seconds?: number | null;
    student: StudentInfo;
}

interface PaginatedAttempts {
    data: AttemptItem[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
    prev_page_url: string | null;
    next_page_url: string | null;
}

interface ExamSessionData {
    id: number;
    title: string | null;
    template?: {
        id: number;
        title: string;
    } | null;
}

interface PageProps {
    session: ExamSessionData;
    attempts: PaginatedAttempts;
    myAttempt: AttemptItem | null;
    currentScope: 'national' | 'branch';
}

export default function Leaderboard({
    session,
    attempts,
    myAttempt,
    currentScope = 'national',
}: PageProps) {
    const sessionTitle =
        session.title ?? session.template?.title ?? 'Sesi Ujian';

    const handleScopeChange = (scope: 'national' | 'branch') => {
        router.get(
            leaderboard.url(session.id, { query: { scope } }),
            {},
            { preserveState: true, preserveScroll: true },
        );
    };

    const formatDuration = (seconds?: number | null) => {
        if (!seconds) {
            return '-';
        }

        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;

        return `${mins}m ${secs}s`;
    };

    const topThree = attempts.data.slice(0, 3);

    return (
        <>
            <Head title={`Leaderboard - ${sessionTitle}`} />

            <div className="space-y-6 px-6 py-6">
                {/* Header Navigation */}
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <Button
                            asChild
                            variant="ghost"
                            size="sm"
                            className="mb-2 -ml-3 gap-1 text-muted-foreground"
                        >
                            <Link href={index.url()}>
                                <ChevronLeft className="h-4 w-4" />
                                Kembali ke Portal CBT
                            </Link>
                        </Button>
                        <Heading
                            title="Leaderboard Ujian CBT"
                            description={`Papan peringkat peserta untuk sesi "${sessionTitle}"`}
                        />
                    </div>

                    {/* Scope Switcher Tabs */}
                    <div className="flex items-center rounded-lg border bg-muted p-1">
                        <Button
                            variant={
                                currentScope === 'national'
                                    ? 'default'
                                    : 'ghost'
                            }
                            size="sm"
                            onClick={() => handleScopeChange('national')}
                            className="gap-1.5"
                        >
                            <Trophy className="h-4 w-4" />
                            Nasional
                        </Button>
                        <Button
                            variant={
                                currentScope === 'branch' ? 'default' : 'ghost'
                            }
                            size="sm"
                            onClick={() => handleScopeChange('branch')}
                            className="gap-1.5"
                        >
                            <MapPin className="h-4 w-4" />
                            Cabang Saya
                        </Button>
                    </div>
                </div>

                {/* My Position Banner (Sticky / Highlight) */}
                {myAttempt && (
                    <Card className="border-primary/40 bg-primary/5">
                        <CardContent className="flex flex-col gap-4 p-4 sm:flex-row sm:items-center sm:justify-between">
                            <div className="flex items-center gap-3">
                                <div className="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary">
                                    <Crown className="h-6 w-6" />
                                </div>
                                <div>
                                    <p className="text-xs font-semibold tracking-wider text-muted-foreground uppercase">
                                        Posisi Peringkat Anda
                                    </p>
                                    <h3 className="text-base font-bold text-foreground">
                                        {currentScope === 'branch'
                                            ? myAttempt.branch_rank
                                                ? `Peringkat #${myAttempt.branch_rank} di Cabang`
                                                : 'Belum terdata di cabang'
                                            : myAttempt.national_rank
                                              ? `Peringkat #${myAttempt.national_rank} Nasional`
                                              : 'Belum masuk peringkat'}
                                    </h3>
                                </div>
                            </div>

                            <div className="flex items-center gap-4 text-sm sm:justify-end">
                                <div>
                                    <span className="text-xs text-muted-foreground">
                                        Skor Total:
                                    </span>
                                    <p className="font-bold text-foreground">
                                        {Number(myAttempt.total_score).toFixed(
                                            2,
                                        )}
                                    </p>
                                </div>
                                <div className="h-8 w-px bg-border" />
                                <div>
                                    <span className="text-xs text-muted-foreground">
                                        Status:
                                    </span>
                                    <p>
                                        <Badge
                                            variant={
                                                myAttempt.is_passed
                                                    ? 'default'
                                                    : 'secondary'
                                            }
                                        >
                                            {myAttempt.is_passed
                                                ? 'Lulus'
                                                : 'Belum Lulus'}
                                        </Badge>
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                )}

                {/* Top 3 Podium (Only on Page 1) */}
                {attempts.current_page === 1 && topThree.length > 0 && (
                    <div className="grid gap-4 sm:grid-cols-3">
                        {topThree.map((item, idx) => {
                            const rank = idx + 1;
                            const isFirst = rank === 1;
                            const isSecond = rank === 2;
                            const isThird = rank === 3;

                            return (
                                <Card
                                    key={item.id}
                                    className={`relative overflow-hidden transition-all hover:shadow-md ${
                                        isFirst
                                            ? 'border-amber-500/50 bg-gradient-to-b from-amber-500/10 via-card to-card'
                                            : isSecond
                                              ? 'border-slate-400/50 bg-gradient-to-b from-slate-400/10 via-card to-card'
                                              : 'border-orange-500/50 bg-gradient-to-b from-orange-500/10 via-card to-card'
                                    }`}
                                >
                                    <CardHeader className="flex flex-row items-center justify-between pb-2">
                                        <div className="flex items-center gap-2">
                                            {isFirst && (
                                                <Medal className="h-6 w-6 text-amber-500" />
                                            )}
                                            {isSecond && (
                                                <Medal className="h-6 w-6 text-slate-400" />
                                            )}
                                            {isThird && (
                                                <Medal className="h-6 w-6 text-orange-500" />
                                            )}
                                            <span className="text-xs font-bold tracking-wider text-muted-foreground uppercase">
                                                Juara {rank}
                                            </span>
                                        </div>
                                        <Badge
                                            variant="outline"
                                            className={
                                                isFirst
                                                    ? 'border-amber-500/50 text-amber-600'
                                                    : isSecond
                                                      ? 'border-slate-400/50 text-slate-500'
                                                      : 'border-orange-500/50 text-orange-600'
                                            }
                                        >
                                            #
                                            {currentScope === 'branch'
                                                ? (item.branch_rank ?? rank)
                                                : (item.national_rank ?? rank)}
                                        </Badge>
                                    </CardHeader>

                                    <CardContent className="space-y-2">
                                        <div className="line-clamp-1 font-semibold text-foreground">
                                            {item.student.name}
                                        </div>
                                        <div className="flex items-center justify-between text-xs text-muted-foreground">
                                            <span>
                                                {item.student.branch?.name ??
                                                    'Pusat'}
                                            </span>
                                            <span className="text-base font-bold text-foreground">
                                                {Number(
                                                    item.total_score,
                                                ).toFixed(2)}
                                            </span>
                                        </div>
                                    </CardContent>
                                </Card>
                            );
                        })}
                    </div>
                )}

                {/* Complete Rankings Table */}
                <div className="overflow-hidden rounded-lg border bg-card">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-16 text-center">
                                    Rank
                                </TableHead>
                                <TableHead>Nama Siswa</TableHead>
                                <TableHead>Cabang</TableHead>
                                <TableHead className="text-center">
                                    Skor
                                </TableHead>
                                <TableHead className="text-center">
                                    Durasi
                                </TableHead>
                                <TableHead className="text-right">
                                    Status
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {attempts.data.length === 0 ? (
                                <TableRow>
                                    <TableCell
                                        colSpan={6}
                                        className="h-32 text-center text-muted-foreground"
                                    >
                                        Belum ada peserta yang menyelesaikan
                                        ujian pada sesi ini.
                                    </TableCell>
                                </TableRow>
                            ) : (
                                attempts.data.map((item) => {
                                    const isMe = myAttempt?.id === item.id;
                                    const rankDisplay =
                                        currentScope === 'branch'
                                            ? item.branch_rank
                                            : item.national_rank;

                                    return (
                                        <TableRow
                                            key={item.id}
                                            className={
                                                isMe
                                                    ? 'bg-primary/5 font-medium'
                                                    : undefined
                                            }
                                        >
                                            <TableCell className="text-center font-bold">
                                                {rankDisplay ? (
                                                    <span
                                                        className={
                                                            rankDisplay === 1
                                                                ? 'text-amber-500'
                                                                : rankDisplay ===
                                                                    2
                                                                  ? 'text-slate-400'
                                                                  : rankDisplay ===
                                                                      3
                                                                    ? 'text-orange-500'
                                                                    : 'text-muted-foreground'
                                                        }
                                                    >
                                                        #{rankDisplay}
                                                    </span>
                                                ) : (
                                                    '-'
                                                )}
                                            </TableCell>
                                            <TableCell>
                                                <div className="flex items-center gap-2">
                                                    <span>
                                                        {item.student.name}
                                                    </span>
                                                    {isMe && (
                                                        <Badge
                                                            variant="secondary"
                                                            className="text-[10px]"
                                                        >
                                                            Anda
                                                        </Badge>
                                                    )}
                                                </div>
                                            </TableCell>
                                            <TableCell className="text-muted-foreground">
                                                {item.student.branch?.name ??
                                                    '-'}
                                            </TableCell>
                                            <TableCell className="text-center font-semibold text-foreground">
                                                {Number(
                                                    item.total_score,
                                                ).toFixed(2)}
                                            </TableCell>
                                            <TableCell className="text-center text-xs text-muted-foreground">
                                                {formatDuration(
                                                    item.duration_seconds,
                                                )}
                                            </TableCell>
                                            <TableCell className="text-right">
                                                <Badge
                                                    variant={
                                                        item.is_passed
                                                            ? 'default'
                                                            : 'secondary'
                                                    }
                                                    className="text-xs"
                                                >
                                                    {item.is_passed
                                                        ? 'Lulus'
                                                        : 'Belum Lulus'}
                                                </Badge>
                                            </TableCell>
                                        </TableRow>
                                    );
                                })
                            )}
                        </TableBody>
                    </Table>

                    {/* Simple Pagination */}
                    {attempts.last_page > 1 && (
                        <div className="flex items-center justify-between border-t px-4 py-3 text-sm">
                            <span className="text-xs text-muted-foreground">
                                Menampilkan {attempts.from ?? 0}–
                                {attempts.to ?? 0} dari {attempts.total} peserta
                            </span>
                            <div className="flex items-center gap-2">
                                <Button
                                    variant="outline"
                                    size="sm"
                                    disabled={!attempts.prev_page_url}
                                    onClick={() =>
                                        attempts.prev_page_url &&
                                        router.get(
                                            attempts.prev_page_url,
                                            {},
                                            { preserveScroll: true },
                                        )
                                    }
                                >
                                    Sebelumnya
                                </Button>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    disabled={!attempts.next_page_url}
                                    onClick={() =>
                                        attempts.next_page_url &&
                                        router.get(
                                            attempts.next_page_url,
                                            {},
                                            { preserveScroll: true },
                                        )
                                    }
                                >
                                    Selanjutnya
                                </Button>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}
