import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Plus, Save, Trash2 } from 'lucide-react';
import type { FormEvent } from 'react';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import {
    DIFFICULTY_OPTIONS,
    GRADING_RULE_OPTIONS,
} from '@/components/question-badges';
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
import { Textarea } from '@/components/ui/textarea';
import { index, store } from '@/routes/questions';

type Subject = {
    id: number;
    name: string;
    code: string | null;
};

type GradingRule = 'STANDARD' | 'TKP';

type OptionForm = {
    option_label: string;
    option_text: string;
    is_correct: boolean;
    weight_score: string;
};

type QuestionForm = {
    subject_id: number | '';
    question_text: string;
    grading_rule: GradingRule;
    difficulty_level: string;
    options: OptionForm[];
};

type PageProps = {
    subjects: {
        data: Subject[];
    };
};

const OPTION_LABELS = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];

/**
 * Validasi server hanya mensyaratkan minimal satu opsi, tapi soal tanpa
 * pilihan yang bisa dipilih tidak berguna. Dua baris adalah batas bawah
 * yang masuk akal untuk form.
 */
const MIN_OPTIONS = 2;

const SUGGESTED_OPTIONS: Record<GradingRule, number> = {
    STANDARD: 4,
    TKP: 5,
};

const NOT_SET = '__none__';

/**
 * Label opsi dibuat dari urutan baris supaya selalu unik dan tidak mungkin
 * melanggar batas `max:10` milik `options.*.option_label`. Isi teks yang
 * sudah diketik tetap dipertahankan saat aturan penilaian berubah.
 */
function buildOptions(
    count: number,
    gradingRule: GradingRule,
    existing: OptionForm[] = [],
): OptionForm[] {
    return Array.from({ length: count }, (_, position) => {
        const previous = existing[position];

        return {
            option_label: OPTION_LABELS[position] ?? String(position + 1),
            option_text: previous?.option_text ?? '',
            is_correct: false,
            weight_score:
                gradingRule === 'TKP' && previous?.weight_score
                    ? previous.weight_score
                    : '3',
        };
    });
}

export default function Create({ subjects }: PageProps) {
    const form = useForm<QuestionForm>({
        subject_id: '',
        question_text: '',
        grading_rule: 'STANDARD',
        difficulty_level: '',
        options: buildOptions(SUGGESTED_OPTIONS.STANDARD, 'STANDARD'),
    });

    const errors = form.errors as unknown as Record<string, string>;
    const isTkp = form.data.grading_rule === 'TKP';

    const updateOption = (position: number, changes: Partial<OptionForm>) => {
        form.setData(
            'options',
            form.data.options.map((option, index) =>
                index === position ? { ...option, ...changes } : option,
            ),
        );
    };

    const handleGradingRuleChange = (value: string) => {
        const gradingRule: GradingRule = value === 'TKP' ? 'TKP' : 'STANDARD';

        form.setData('grading_rule', gradingRule);
        form.setData(
            'options',
            buildOptions(
                SUGGESTED_OPTIONS[gradingRule],
                gradingRule,
                form.data.options,
            ),
        );
    };

    const markCorrect = (position: number) => {
        form.setData(
            'options',
            form.data.options.map((option, index) => ({
                ...option,
                is_correct: index === position,
            })),
        );
    };

    const addOption = () => {
        const position = form.data.options.length;

        form.setData('options', [
            ...form.data.options,
            {
                option_label: OPTION_LABELS[position] ?? String(position + 1),
                option_text: '',
                is_correct: false,
                weight_score: form.data.grading_rule === 'TKP' ? '3' : '0',
            },
        ]);
    };

    const removeOption = (position: number) => {
        if (form.data.options.length <= MIN_OPTIONS) {
            return;
        }

        form.setData(
            'options',
            form.data.options
                .filter((_, index) => index !== position)
                .map((option, index) => ({
                    ...option,
                    option_label: OPTION_LABELS[index] ?? String(index + 1),
                })),
        );
    };

    const handleSubmit = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();

        form.post(store.url(), { preserveScroll: true });
    };

    return (
        <>
            <Head title="Tambah Soal" />

            <h1 className="sr-only">Tambah Soal</h1>

            <div className="space-y-6 px-6 py-6">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <Heading
                        title="Tambah Soal"
                        description="Buat soal baru beserta pilihan jawabannya."
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
                                Informasi Soal
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="subject_id">
                                    Mata Pelajaran
                                </Label>
                                <Select
                                    value={
                                        form.data.subject_id === ''
                                            ? NOT_SET
                                            : String(form.data.subject_id)
                                    }
                                    onValueChange={(value) =>
                                        form.setData(
                                            'subject_id',
                                            value === NOT_SET
                                                ? ''
                                                : Number(value),
                                        )
                                    }
                                >
                                    <SelectTrigger
                                        id="subject_id"
                                        className="w-full"
                                        aria-invalid={
                                            errors.subject_id
                                                ? 'true'
                                                : undefined
                                        }
                                    >
                                        <SelectValue placeholder="Pilih mata pelajaran" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {subjects.data.map((subject) => (
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
                                <InputError message={errors.subject_id} />
                            </div>

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="question_text">
                                    Pertanyaan
                                </Label>
                                <Textarea
                                    id="question_text"
                                    name="question_text"
                                    rows={4}
                                    value={form.data.question_text}
                                    onChange={(event) =>
                                        form.setData(
                                            'question_text',
                                            event.target.value,
                                        )
                                    }
                                    placeholder="Tulis pertanyaan di sini."
                                    aria-invalid={
                                        errors.question_text
                                            ? 'true'
                                            : undefined
                                    }
                                />
                                <InputError message={errors.question_text} />
                            </div>

                            <div className="grid gap-4 sm:grid-cols-2">
                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="grading_rule">
                                        Aturan Penilaian
                                    </Label>
                                    <Select
                                        value={form.data.grading_rule}
                                        onValueChange={handleGradingRuleChange}
                                    >
                                        <SelectTrigger
                                            id="grading_rule"
                                            className="w-full"
                                            aria-invalid={
                                                errors.grading_rule
                                                    ? 'true'
                                                    : undefined
                                            }
                                        >
                                            <SelectValue />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {GRADING_RULE_OPTIONS.map(
                                                (option) => (
                                                    <SelectItem
                                                        key={option.value}
                                                        value={option.value}
                                                    >
                                                        {option.label}
                                                    </SelectItem>
                                                ),
                                            )}
                                        </SelectContent>
                                    </Select>
                                    <InputError message={errors.grading_rule} />
                                </div>

                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="difficulty_level">
                                        Tingkat Kesulitan
                                    </Label>
                                    <Select
                                        value={
                                            form.data.difficulty_level ||
                                            NOT_SET
                                        }
                                        onValueChange={(value) =>
                                            form.setData(
                                                'difficulty_level',
                                                value === NOT_SET ? '' : value,
                                            )
                                        }
                                    >
                                        <SelectTrigger
                                            id="difficulty_level"
                                            className="w-full"
                                            aria-invalid={
                                                errors.difficulty_level
                                                    ? 'true'
                                                    : undefined
                                            }
                                        >
                                            <SelectValue placeholder="Pilih tingkat" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {DIFFICULTY_OPTIONS.map(
                                                (option) => (
                                                    <SelectItem
                                                        key={option.value}
                                                        value={option.value}
                                                    >
                                                        {option.label}
                                                    </SelectItem>
                                                ),
                                            )}
                                        </SelectContent>
                                    </Select>
                                    <InputError
                                        message={errors.difficulty_level}
                                    />
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle className="text-base">
                                Pilihan Jawaban
                            </CardTitle>
                            <CardDescription>
                                {isTkp
                                    ? 'Setiap opsi diberi bobot 1 sampai 5. Tidak ada kunci jawaban tunggal.'
                                    : 'Tandai satu opsi sebagai kunci jawaban.'}
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            {errors.options && (
                                <div className="rounded-md border border-destructive/40 bg-destructive/10 px-3 py-2 text-sm text-destructive">
                                    {errors.options}
                                </div>
                            )}

                            {form.data.options.map((option, position) => {
                                const textErrorKey = `options.${position}.option_text`;
                                const weightErrorKey = `options.${position}.weight_score`;

                                return (
                                    <div
                                        key={position}
                                        className="rounded-lg border border-border p-4"
                                    >
                                        <div className="flex items-start gap-3">
                                            {!isTkp && (
                                                <input
                                                    type="radio"
                                                    name="correct_option"
                                                    className="mt-2.5 size-4 shrink-0 accent-primary"
                                                    checked={option.is_correct}
                                                    onChange={() =>
                                                        markCorrect(position)
                                                    }
                                                    aria-label={`Tandai opsi ${option.option_label} sebagai kunci jawaban`}
                                                />
                                            )}

                                            <div className="flex-1 space-y-1.5">
                                                <Label
                                                    htmlFor={`option-text-${position}`}
                                                    className="text-muted-foreground"
                                                >
                                                    Opsi {option.option_label}
                                                </Label>
                                                <Textarea
                                                    id={`option-text-${position}`}
                                                    rows={2}
                                                    value={option.option_text}
                                                    onChange={(event) =>
                                                        updateOption(position, {
                                                            option_text:
                                                                event.target
                                                                    .value,
                                                        })
                                                    }
                                                    placeholder="Tulis pilihan jawaban."
                                                    aria-invalid={
                                                        errors[textErrorKey]
                                                            ? 'true'
                                                            : undefined
                                                    }
                                                />
                                                <InputError
                                                    message={
                                                        errors[textErrorKey]
                                                    }
                                                />
                                            </div>

                                            {isTkp && (
                                                <div className="w-24 space-y-1.5">
                                                    <Label
                                                        htmlFor={`option-weight-${position}`}
                                                    >
                                                        Bobot
                                                    </Label>
                                                    <Input
                                                        id={`option-weight-${position}`}
                                                        type="number"
                                                        min={1}
                                                        max={5}
                                                        step={1}
                                                        value={
                                                            option.weight_score
                                                        }
                                                        onChange={(event) =>
                                                            updateOption(
                                                                position,
                                                                {
                                                                    weight_score:
                                                                        event
                                                                            .target
                                                                            .value,
                                                                },
                                                            )
                                                        }
                                                        aria-invalid={
                                                            errors[
                                                                weightErrorKey
                                                            ]
                                                                ? 'true'
                                                                : undefined
                                                        }
                                                    />
                                                    <InputError
                                                        message={
                                                            errors[
                                                                weightErrorKey
                                                            ]
                                                        }
                                                    />
                                                </div>
                                            )}

                                            <Button
                                                type="button"
                                                variant="ghost"
                                                size="icon-sm"
                                                onClick={() =>
                                                    removeOption(position)
                                                }
                                                disabled={
                                                    form.data.options.length <=
                                                    MIN_OPTIONS
                                                }
                                                aria-label={`Hapus opsi ${option.option_label}`}
                                                className="mt-6 text-muted-foreground hover:text-destructive"
                                            >
                                                <Trash2 />
                                            </Button>
                                        </div>
                                    </div>
                                );
                            })}

                            <Button
                                type="button"
                                variant="outline"
                                size="sm"
                                onClick={addOption}
                            >
                                <Plus />
                                Tambah Opsi
                            </Button>
                        </CardContent>
                    </Card>

                    <div className="flex items-center justify-end gap-2">
                        <Button type="button" variant="outline" asChild>
                            <Link href={index.url()}>Batal</Link>
                        </Button>
                        <Button type="submit" disabled={form.processing}>
                            {form.processing ? <Spinner /> : <Save />}
                            Simpan Soal
                        </Button>
                    </div>
                </form>
            </div>
        </>
    );
}
