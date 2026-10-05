import { router } from '@inertiajs/react';
import { RotateCcw } from 'lucide-react';
import { useState } from 'react';
import {
    DIFFICULTY_OPTIONS,
    GRADING_RULE_OPTIONS,
} from '@/components/question-badges';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { index } from '@/routes/questions';

type Subject = {
    id: number;
    name: string;
    code: string | null;
};

type Filters = {
    search: string | null;
    subject_id: string | null;
    difficulty_level: string | null;
    grading_rule: string | null;
    per_page: number;
};

const ALL = 'all';

export default function QuestionFilterBar({
    subjects,
    filters,
}: {
    subjects: Subject[];
    filters: Filters;
}) {
    const [search, setSearch] = useState(filters.search ?? '');

    const hasActiveFilter =
        filters.search !== null ||
        filters.subject_id !== null ||
        filters.difficulty_level !== null ||
        filters.grading_rule !== null;

    /**
     * Kirim filter baru ke server. `mergeQuery` dipakai supaya filter lain
     * tetap ada di URL, dan `page: null` mengembalikan ke halaman 1 karena
     * nomor halaman lama belum tentu masih valid untuk hasil yang baru.
     */
    const applyFilter = (changes: Record<string, string | null>) => {
        router.get(index.url({ mergeQuery: { ...changes, page: null } }));
    };

    const handleSubmit = (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();

        const term = search.trim();

        applyFilter({ search: term === '' ? null : term });
    };

    const handleReset = () => {
        setSearch('');
        router.get(index.url());
    };

    return (
        <div className="flex flex-col gap-3 rounded-lg border border-border bg-card p-4 xl:flex-row xl:items-end">
            <form
                onSubmit={handleSubmit}
                className="flex flex-col gap-1.5 xl:flex-1"
            >
                <Label htmlFor="question-search">Cari Soal</Label>
                <div className="flex gap-2">
                    <Input
                        id="question-search"
                        name="search"
                        value={search}
                        onChange={(event) => setSearch(event.target.value)}
                        placeholder="Kata kunci, misalnya: Pancasila"
                        autoComplete="off"
                    />
                    <Button type="submit">Cari</Button>
                </div>
            </form>

            <div className="flex flex-col gap-1.5 xl:w-56">
                <Label htmlFor="question-subject">Mata Pelajaran</Label>
                <Select
                    value={filters.subject_id ?? ALL}
                    onValueChange={(value) =>
                        applyFilter({
                            subject_id: value === ALL ? null : value,
                        })
                    }
                >
                    <SelectTrigger id="question-subject" className="w-full">
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value={ALL}>
                            Semua Mata Pelajaran
                        </SelectItem>
                        {subjects.map((subject) => (
                            <SelectItem
                                key={subject.id}
                                value={String(subject.id)}
                            >
                                {subject.code
                                    ? `${subject.code} - ${subject.name}`
                                    : subject.name}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
            </div>

            <div className="flex flex-col gap-1.5 xl:w-48">
                <Label htmlFor="question-difficulty">Tingkat Kesulitan</Label>
                <Select
                    value={filters.difficulty_level ?? ALL}
                    onValueChange={(value) =>
                        applyFilter({
                            difficulty_level: value === ALL ? null : value,
                        })
                    }
                >
                    <SelectTrigger id="question-difficulty" className="w-full">
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value={ALL}>Semua Tingkat</SelectItem>
                        {DIFFICULTY_OPTIONS.map((option) => (
                            <SelectItem key={option.value} value={option.value}>
                                {option.label}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
            </div>

            <div className="flex flex-col gap-1.5 xl:w-44">
                <Label htmlFor="question-grading-rule">Aturan Penilaian</Label>
                <Select
                    value={filters.grading_rule ?? ALL}
                    onValueChange={(value) =>
                        applyFilter({
                            grading_rule: value === ALL ? null : value,
                        })
                    }
                >
                    <SelectTrigger
                        id="question-grading-rule"
                        className="w-full"
                    >
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value={ALL}>Semua Aturan</SelectItem>
                        {GRADING_RULE_OPTIONS.map((option) => (
                            <SelectItem key={option.value} value={option.value}>
                                {option.label}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
            </div>

            {hasActiveFilter && (
                <Button
                    type="button"
                    variant="ghost"
                    onClick={handleReset}
                    className="xl:mb-0.5"
                >
                    <RotateCcw />
                    Reset
                </Button>
            )}
        </div>
    );
}
