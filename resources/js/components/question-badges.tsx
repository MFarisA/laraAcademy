import { Badge } from '@/components/ui/badge';

type GradingRule = 'STANDARD' | 'TKP';

type BadgeStyle = {
    label: string;
    className: string;
};

export const GRADING_RULE_LABEL: Record<GradingRule, BadgeStyle> = {
    STANDARD: {
        label: 'Standard',
        className:
            'border-blue-500/30 bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400 dark:border-blue-800',
    },
    TKP: {
        label: 'TKP',
        className:
            'border-purple-500/30 bg-purple-50 text-purple-700 dark:bg-purple-950/40 dark:text-purple-400 dark:border-purple-800',
    },
};

export const DIFFICULTY_CONFIG: Record<string, BadgeStyle> = {
    easy: {
        label: 'Mudah',
        className:
            'border-emerald-500/30 bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800',
    },
    medium: {
        label: 'Sedang',
        className:
            'border-amber-500/30 bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800',
    },
    hard: {
        label: 'Sulit',
        className:
            'border-rose-500/30 bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-800',
    },
};

export const DIFFICULTY_OPTIONS = [
    { value: 'easy', label: 'Mudah' },
    { value: 'medium', label: 'Sedang' },
    { value: 'hard', label: 'Sulit' },
];

export const GRADING_RULE_OPTIONS = [
    { value: 'STANDARD', label: 'Standard' },
    { value: 'TKP', label: 'TKP' },
];

export function GradingRuleBadge({
    gradingRule,
}: {
    gradingRule: GradingRule;
}) {
    const style = GRADING_RULE_LABEL[gradingRule];

    return (
        <Badge
            variant="outline"
            className={style?.className ?? 'text-muted-foreground'}
        >
            {style?.label ?? gradingRule}
        </Badge>
    );
}

export function DifficultyBadge({
    difficultyLevel,
}: {
    difficultyLevel: string;
}) {
    const style = DIFFICULTY_CONFIG[difficultyLevel];

    return (
        <Badge
            variant="outline"
            className={style?.className ?? 'text-muted-foreground'}
        >
            {style?.label ?? difficultyLevel}
        </Badge>
    );
}
