<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use SplFileObject;

class CsvCatalogImporter
{
    /**
     * Import simple code/name catalogs from CSV.
     *
     * @param  callable(array{code: string, name: string}): ('created'|'updated'|'skipped')  $upsert
     * @return array{created: int, updated: int, skipped: int}
     */
    public function import(UploadedFile $uploadedFile, callable $upsert): array
    {
        $file = new SplFileObject($uploadedFile->getRealPath());
        $file->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY);

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $isFirstRow = true;
        $useHeader = false;
        $headerMap = [];

        foreach ($file as $row) {
            if (! is_array($row)) {
                continue;
            }

            $normalizedRow = array_map(
                static fn ($value) => is_string($value) ? trim($value) : '',
                $row,
            );

            if ($this->isCsvRowEmpty($normalizedRow)) {
                continue;
            }

            if ($isFirstRow) {
                $isFirstRow = false;
                $lowerRow = array_map(static fn (string $value) => strtolower($value), $normalizedRow);

                if (in_array('code', $lowerRow, true) && in_array('name', $lowerRow, true)) {
                    $useHeader = true;
                    $headerMap = array_flip($lowerRow);
                    if (! isset($headerMap['code'], $headerMap['name'])) {
                        $skipped++;
                        continue;
                    }
                    continue;
                }
            }

            $data = $useHeader
                ? [
                    'code' => $normalizedRow[$headerMap['code']] ?? '',
                    'name' => $normalizedRow[$headerMap['name']] ?? '',
                ]
                : [
                    'code' => $normalizedRow[0] ?? '',
                    'name' => $normalizedRow[1] ?? '',
                ];

            $data['code'] = strtoupper($data['code']);

            $validator = Validator::make($data, [
                'code' => ['required', 'string', 'max:100'],
                'name' => ['required', 'string', 'max:255'],
            ]);

            if ($validator->fails()) {
                $skipped++;
                continue;
            }

            $result = $upsert($data);

            if ($result === 'created') {
                $created++;
                continue;
            }

            if ($result === 'updated') {
                $updated++;
                continue;
            }

            $skipped++;
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
        ];
    }

    /**
     * Determine whether a CSV row is effectively empty.
     *
     * @param  array<int, string>  $row
     */
    private function isCsvRowEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if ($value !== '') {
                return false;
            }
        }

        return true;
    }
}
