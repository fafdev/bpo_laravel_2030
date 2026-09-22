import { usePage } from '@inertiajs/react';

interface TranslationMap {
    [key: string]: string | TranslationMap;
}

type TranslationNode = string | TranslationMap;

type SharedTranslations = {
    locale?: string;
    translations?: TranslationMap;
};

function getByDotPath(
    source: TranslationMap | undefined,
    path: string,
): string | undefined {
    if (!source) {
        return undefined;
    }

    const value = path
        .split('.')
        .reduce<TranslationNode | undefined>((acc, part) => {
            if (!acc || typeof acc === 'string') {
                return undefined;
            }

            return acc[part];
        }, source);

    return typeof value === 'string' ? value : undefined;
}

export function useTranslations() {
    const { locale, translations } = usePage<SharedTranslations>().props;

    const t = (key: string, fallback?: string): string => {
        return getByDotPath(translations, key) ?? fallback ?? key;
    };

    return { locale, t };
}
