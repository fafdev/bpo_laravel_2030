import { Head, usePage } from '@inertiajs/react';
import CatalogManagementPage from '@/components/catalog-management-page';
import { useTranslations } from '@/hooks/use-translations';
import { dashboard } from '@/routes';
import rolsRoutes, { index } from '@/routes/rols';

type RolItem = {
    id: number;
    code: string;
    name: string;
};

type Props = {
    rols: RolItem[];
};

function getText(
    translations: { ui?: { dashboard?: string; roles?: string } } | undefined,
    key: 'dashboard' | 'roles',
    fallback: string,
): string {
    return translations?.ui?.[key] ?? fallback;
}

export default function RolsIndex({ rols }: Props) {
    const { t } = useTranslations();
    const { currentTeam } = usePage<{ currentTeam?: { slug: string } | null }>()
        .props;

    if (!currentTeam) {
        return null;
    }

    return (
        <>
            <Head title={t('ui.roles', 'Roles')} />

            <h1 className="sr-only">{t('ui.roles', 'Roles')}</h1>

            <CatalogManagementPage
                items={rols}
                idPrefix="rol"
                copy={{
                    title: t('ui.roles', 'Roles'),
                    description: 'Crea, edita o elimina roles.',
                    createTitle: 'Nuevo rol',
                    createDescription: 'Alta manual de un rol.',
                    codePlaceholder: 'ADMIN',
                    namePlaceholder: 'Administrador',
                    createSubmit: 'Guardar rol',
                    importTitle: 'Importar CSV',
                    importDescription:
                        'Cabeceras opcionales: code,name. Sin cabecera, se usa columna 1 para codigo y 2 para nombre.',
                    importSubmit: 'Importar',
                    emptyState: 'No hay roles cargados.',
                    updateSubmit: 'Actualizar',
                    deleteSubmit: 'Eliminar',
                }}
                storeForm={rolsRoutes.store.form(currentTeam.slug)}
                importForm={rolsRoutes.import.form(currentTeam.slug)}
                updateFormFor={(id) =>
                    rolsRoutes.update.form([currentTeam.slug, id])
                }
                destroyFormFor={(id) =>
                    rolsRoutes.destroy.form([currentTeam.slug, id])
                }
            />
        </>
    );
}

RolsIndex.layout = (props: {
    currentTeam?: { slug: string } | null;
    translations?: {
        ui?: {
            dashboard?: string;
            roles?: string;
        };
    };
}) => ({
    breadcrumbs: [
        {
            title: getText(props.translations, 'dashboard', 'Dashboard'),
            href: props.currentTeam ? dashboard(props.currentTeam.slug) : '/',
        },
        {
            title: getText(props.translations, 'roles', 'Roles'),
            href: props.currentTeam ? index(props.currentTeam.slug) : '/',
        },
    ],
});
