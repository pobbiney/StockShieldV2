<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\UnitOfMeasure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class ItemsImport implements ToCollection, WithCalculatedFormulas
{
    public int $importedCount = 0;

    public int $skippedCount = 0;

    /** @var array<string, int> */
    public array $skipReasons = [];

    protected Collection $categories;

    protected Collection $units;

    public function __construct(
        protected ?int $defaultStoreId = null,
        protected ?int $createdBy = null,
    ) {
        $this->categories = ItemCategory::pluck('id', 'name');
        $this->units = UnitOfMeasure::pluck('id', 'name');
    }

    public function collection(Collection $rows): void
    {
        $rows = $rows
            ->map(fn ($row) => $row instanceof Collection ? $row->values()->all() : array_values((array) $row))
            ->filter(fn (array $row) => $this->rowHasData($row))
            ->values();

        if ($rows->isEmpty()) {
            $this->recordSkip('spreadsheet is empty');

            return;
        }

        [$headerMap, $dataRows] = $this->resolveRows($rows);

        foreach ($dataRows as $row) {
            $this->importRow($this->mapRow($row, $headerMap));
        }
    }

    protected function resolveRows(Collection $rows): array
    {
        $firstRow = $rows->first();
        $headerMap = $this->buildHeaderMap($firstRow);

        if ($headerMap !== null) {
            return [$headerMap, $rows->slice(1)->values()];
        }

        return [[
            0 => 'name',
            1 => 'category_id',
            2 => 'unit_id',
            3 => 'store_id',
            4 => 'status',
            5 => 'reorder_level',
        ], $rows];
    }

    protected function buildHeaderMap(array $row): ?array
    {
        $labels = array_map(fn ($value) => $this->normalizeKey($value), $row);
        $known = ['name', 'item_name', 'item', 'category_id', 'cat_id', 'category', 'unit_id', 'unit', 'store_id', 'store', 'status'];

        if (count(array_intersect($labels, $known)) === 0) {
            return null;
        }

        $map = [];

        foreach ($labels as $index => $label) {
            if ($label !== '') {
                $map[$index] = $label;
            }
        }

        return $map;
    }

    protected function mapRow(array $row, array $headerMap): array
    {
        $mapped = [];

        foreach ($headerMap as $index => $field) {
            $mapped[$field] = $row[$index] ?? null;
        }

        return $mapped;
    }

    protected function importRow(array $row): void
    {
        $name = $this->cell($row, ['name', 'item_name', 'item']);

        if ($name === null || $name === '') {
            $this->recordSkip('missing item name');

            return;
        }

        $storeId = $this->cell($row, ['store_id', 'store']) ?? $this->defaultStoreId;

        if (!$storeId) {
            $this->recordSkip('missing store (add store_id column or log into a store)');

            return;
        }

        $storeId = (string) (int) $storeId;
        $name = trim((string) $name);

        $unitId = $this->resolveUnitId($this->cell($row, ['unit_id', 'unit_of_measure_id', 'unit', 'unit_of_issue']));

        if (!$unitId) {
            $this->recordSkip('missing or unknown unit (use unit_id or unit name)');

            return;
        }

        if (Item::where('name', $name)->where('store_id', $storeId)->exists()) {
            $this->recordSkip('duplicate item in store');

            return;
        }

        $lastItem = Item::latest('id')->first();
        $number = $lastItem ? $lastItem->id + 1 : 1;

        Item::create([
            'item_code'     => 'ITM-' . str_pad((string) $number, 5, '0', STR_PAD_LEFT),
            'name'          => $name,
            'cat_id'        => $this->resolveCategoryId($this->cell($row, ['category_id', 'cat_id', 'category'])),
            'unit_id'       => $unitId,
            'store_id'      => $storeId,
            'reorder_level' => $this->cell($row, ['reorder_level', 're_order_level']) ?? 0,
            'status'        => $this->cell($row, ['status']) ?? 'Active',
            'created_by'    => $this->createdBy ?? Auth::id(),
        ]);

        $this->importedCount++;
    }

    protected function resolveCategoryId(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        $name = trim((string) $value);

        foreach ($this->categories as $categoryName => $id) {
            if (strcasecmp((string) $categoryName, $name) === 0) {
                return (int) $id;
            }
        }

        return null;
    }

    protected function resolveUnitId(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            $id = (int) $value;

            return UnitOfMeasure::whereKey($id)->exists() ? $id : null;
        }

        $name = trim((string) $value);

        foreach ($this->units as $unitName => $id) {
            if (strcasecmp((string) $unitName, $name) === 0) {
                return (int) $id;
            }
        }

        return null;
    }

    protected function cell(array $row, array $keys): mixed
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $row)) {
                continue;
            }

            $value = $this->normalizeValue($row[$key]);

            if ($value === null || $value === '') {
                continue;
            }

            return $value;
        }

        return null;
    }

    protected function normalizeKey(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        return Str::slug(strtolower(trim((string) $value)), '_');
    }

    protected function normalizeValue(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        if (is_numeric($value) && !is_string($value)) {
            return (string) (int) $value;
        }

        if (is_string($value)) {
            $trimmed = trim($value);

            return $trimmed === '' ? null : $trimmed;
        }

        return $value;
    }

    protected function rowHasData(array $row): bool
    {
        foreach ($row as $value) {
            if ($value !== null && trim((string) $value) !== '') {
                return true;
            }
        }

        return false;
    }

    protected function recordSkip(string $reason): void
    {
        $this->skippedCount++;
        $this->skipReasons[$reason] = ($this->skipReasons[$reason] ?? 0) + 1;
    }
}
