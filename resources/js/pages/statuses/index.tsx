import { Head, usePage } from '@inertiajs/react';
import CatalogManagementPage from '@/components/catalog-management-page';
import { useTranslations } from '@/hooks/use-translations';
import { dashboard } from '@/routes';
import statusesRoutes, { index } from '@/routes/statuses';

type StatusItem = {
    id: number;
    code: string;
    name: string;
};

type Props = {
    statuses: StatusItem[];
};

function getText(
    translations:
        | { ui?: { dashboard?: string; statuses?: string } }
        | undefined,
    key: 'dashboard' | 'statuses',
    fallback: string,
): string {
    return translations?.ui?.[key] ?? fallback;
}

export default function StatusesIndex({ statuses }: Props) {
    const { t } = useTranslations();
    const { currentTeam } = usePage<{ currentTeam?: { slug: string } | null }>()
        .props;

    if (!currentTeam) {
        return null;
    }

    return (
        <>
            <Head title={t('ui.statuses', 'Statuses')} />

            <h1 className="sr-only">{t('ui.statuses', 'Statuses')}</h1>

            <CatalogManagementPage
                items={statuses}
                idPrefix="status"
                copy={{
                    title: t('ui.statuses', 'Statuses'),
                    description: 'Crea, edita o elimina estados.',
                    createTitle: 'Nuevo estado',
                    createDescription: 'Alta manual de un estado.',
                    codePlaceholder: 'OPEN',
                    namePlaceholder: 'Abierto',
                    createSubmit: 'Guardar estado',
                    importTitle: 'Importar CSV',
                    importDescription:
                        'Cabeceras opcionales: code,name. Sin cabecera, se usa columna 1 para codigo y 2 para nombre.',
                    importSubmit: 'Importar',
                    emptyState: 'No hay estados cargados.',
                    updateSubmit: 'Actualizar',
                    deleteSubmit: 'Eliminar',
                }}
                storeForm={statusesRoutes.store.form(currentTeam.slug)}
                importForm={statusesRoutes.import.form(currentTeam.slug)}
                updateFormFor={(id) =>
                    statusesRoutes.update.form([currentTeam.slug, id])
                }
                destroyFormFor={(id) =>
                    statusesRoutes.destroy.form([currentTeam.slug, id])
                }
            />
        </>
    );
}

StatusesIndex.layout = (props: {
    currentTeam?: { slug: string } | null;
    translations?: {
        ui?: {
            dashboard?: string;
            statuses?: string;
        };
    };
}) => ({
    breadcrumbs: [
        {
            title: getText(props.translations, 'dashboard', 'Dashboard'),
            href: props.currentTeam ? dashboard(props.currentTeam.slug) : '/',
        },
        {
            title: getText(props.translations, 'statuses', 'Statuses'),
            href: props.currentTeam ? index(props.currentTeam.slug) : '/',
        },
    ],
});
