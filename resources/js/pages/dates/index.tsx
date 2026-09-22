import { Head, usePage } from '@inertiajs/react';
import CatalogManagementPage from '@/components/catalog-management-page';
import { useTranslations } from '@/hooks/use-translations';
import { dashboard } from '@/routes';
import datesRoutes, { index } from '@/routes/dates';

type DateItem = {
    id: number;
    code: string;
    name: string;
};

type Props = {
    dates: DateItem[];
};

function getText(
    translations:
        | { ui?: { dashboard?: string; dates?: string } }
        | undefined,
    key: 'dashboard' | 'dates',
    fallback: string,
): string {
    return translations?.ui?.[key] ?? fallback;
}

export default function DatesIndex({ dates }: Props) {
    const { t } = useTranslations();
    const { currentTeam } = usePage<{ currentTeam?: { slug: string } | null }>()
        .props;

    if (!currentTeam) {
        return null;
    }

    return (
        <>
            <Head title={t('ui.dates', 'Dates')} />

            <h1 className="sr-only">{t('ui.dates', 'Dates')}</h1>

            <CatalogManagementPage
                items={dates}
                idPrefix="date"
                copy={{
                    title: t('ui.dates', 'Dates'),
                    description: 'Crea, edita o elimina fechas.',
                    createTitle: 'Nueva fecha',
                    createDescription: 'Alta manual de una fecha.',
                    codePlaceholder: 'DATE001',
                    namePlaceholder: 'Fecha especial',
                    createSubmit: 'Guardar fecha',
                    importTitle: 'Importar CSV',
                    importDescription:
                        'Cabeceras opcionales: code,name. Sin cabecera, se usa columna 1 para codigo y 2 para nombre.',
                    importSubmit: 'Importar',
                    emptyState: 'No hay fechas cargadas.',
                    updateSubmit: 'Actualizar',
                    deleteSubmit: 'Eliminar',
                }}
                storeForm={datesRoutes.store.form(currentTeam.slug)}
                importForm={datesRoutes.import.form(currentTeam.slug)}
                updateFormFor={(id) =>
                    datesRoutes.update.form([currentTeam.slug, id])
                }
                destroyFormFor={(id) =>
                    datesRoutes.destroy.form([currentTeam.slug, id])
                }
            />
        </>
    );
}

DatesIndex.layout = (props: {
    currentTeam?: { slug: string } | null;
    translations?: {
        ui?: {
            dashboard?: string;
            dates?: string;
        };
    };
}) => ({
    breadcrumbs: [
        {
            title: getText(props.translations, 'dashboard', 'Dashboard'),
            href: props.currentTeam ? dashboard(props.currentTeam.slug) : '/',
        },
        {
            title: getText(props.translations, 'dates', 'Dates'),
            href: props.currentTeam ? index(props.currentTeam.slug) : '/',
        },
    ],
});
