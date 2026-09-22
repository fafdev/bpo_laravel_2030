import { Form } from '@inertiajs/react';
import { Pencil, Plus, Trash2, Upload } from 'lucide-react';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type CatalogItem = {
    id: number;
    code: string;
    name: string;
};

type FormAction = {
    action: string;
    method: 'get' | 'post' | 'put' | 'patch' | 'delete';
};

type Copy = {
    title: string;
    description: string;
    createTitle: string;
    createDescription: string;
    codePlaceholder: string;
    namePlaceholder: string;
    createSubmit: string;
    importTitle: string;
    importDescription: string;
    importSubmit: string;
    emptyState: string;
    updateSubmit: string;
    deleteSubmit: string;
};

type Props = {
    items: CatalogItem[];
    copy: Copy;
    idPrefix: string;
    storeForm: FormAction;
    importForm: FormAction;
    updateFormFor: (id: number) => FormAction;
    destroyFormFor: (id: number) => FormAction;
};

export default function CatalogManagementPage({
    items,
    copy,
    idPrefix,
    storeForm,
    importForm,
    updateFormFor,
    destroyFormFor,
}: Props) {
    return (
        <div className="space-y-8 px-4 py-6">
            <Heading
                variant="small"
                title={copy.title}
                description={copy.description}
            />

            <div className="grid gap-4 lg:grid-cols-2">
                <div className="rounded-lg border p-4">
                    <h2 className="text-base font-semibold">{copy.createTitle}</h2>
                    <p className="text-muted-foreground mt-1 text-sm">
                        {copy.createDescription}
                    </p>

                    <Form {...storeForm} className="mt-4 space-y-4" resetOnSuccess>
                        {({ errors, processing }) => (
                            <>
                                <div className="grid gap-2">
                                    <Label htmlFor={`${idPrefix}-new-code`}>
                                        Codigo
                                    </Label>
                                    <Input
                                        id={`${idPrefix}-new-code`}
                                        name="code"
                                        placeholder={copy.codePlaceholder}
                                        required
                                    />
                                    <InputError message={errors.code} />
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor={`${idPrefix}-new-name`}>
                                        Nombre
                                    </Label>
                                    <Input
                                        id={`${idPrefix}-new-name`}
                                        name="name"
                                        placeholder={copy.namePlaceholder}
                                        required
                                    />
                                    <InputError message={errors.name} />
                                </div>

                                <Button type="submit" disabled={processing}>
                                    <Plus className="h-4 w-4" />
                                    {copy.createSubmit}
                                </Button>
                            </>
                        )}
                    </Form>
                </div>

                <div className="rounded-lg border p-4">
                    <h2 className="text-base font-semibold">{copy.importTitle}</h2>
                    <p className="text-muted-foreground mt-1 text-sm">
                        {copy.importDescription}
                    </p>

                    <Form
                        {...importForm}
                        encType="multipart/form-data"
                        className="mt-4 space-y-4"
                        resetOnSuccess
                    >
                        {({ errors, processing }) => (
                            <>
                                <div className="grid gap-2">
                                    <Label htmlFor={`${idPrefix}-csv`}>
                                        Fichero CSV
                                    </Label>
                                    <Input
                                        id={`${idPrefix}-csv`}
                                        name="file"
                                        type="file"
                                        accept=".csv,text/csv,.txt"
                                        required
                                    />
                                    <InputError message={errors.file} />
                                </div>

                                <Button type="submit" disabled={processing}>
                                    <Upload className="h-4 w-4" />
                                    {copy.importSubmit}
                                </Button>
                            </>
                        )}
                    </Form>
                </div>
            </div>

            <div className="space-y-3">
                {items.map((item) => (
                    <div
                        key={item.id}
                        className="flex flex-col gap-3 rounded-lg border p-4 lg:flex-row lg:items-end lg:justify-between"
                    >
                        <Form
                            {...updateFormFor(item.id)}
                            className="grid flex-1 gap-3 md:grid-cols-2"
                        >
                            {({ errors, processing }) => (
                                <>
                                    <div className="grid gap-2">
                                        <Label htmlFor={`${idPrefix}-code-${item.id}`}>
                                            Codigo
                                        </Label>
                                        <Input
                                            id={`${idPrefix}-code-${item.id}`}
                                            name="code"
                                            defaultValue={item.code}
                                            required
                                        />
                                        <InputError message={errors.code} />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor={`${idPrefix}-name-${item.id}`}>
                                            Nombre
                                        </Label>
                                        <Input
                                            id={`${idPrefix}-name-${item.id}`}
                                            name="name"
                                            defaultValue={item.name}
                                            required
                                        />
                                        <InputError message={errors.name} />
                                    </div>

                                    <div className="md:col-span-2">
                                        <Button
                                            type="submit"
                                            variant="secondary"
                                            disabled={processing}
                                        >
                                            <Pencil className="h-4 w-4" />
                                            {copy.updateSubmit}
                                        </Button>
                                    </div>
                                </>
                            )}
                        </Form>

                        <Form {...destroyFormFor(item.id)}>
                            {({ processing }) => (
                                <Button
                                    type="submit"
                                    variant="destructive"
                                    disabled={processing}
                                >
                                    <Trash2 className="h-4 w-4" />
                                    {copy.deleteSubmit}
                                </Button>
                            )}
                        </Form>
                    </div>
                ))}

                {items.length === 0 ? (
                    <div className="text-muted-foreground rounded-lg border border-dashed p-8 text-center text-sm">
                        {copy.emptyState}
                    </div>
                ) : null}
            </div>
        </div>
    );
}
