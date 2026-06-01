<?php

namespace App\Imports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ItemsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $lastItem = Item::latest()->first();
        $number = $lastItem ? $lastItem->id + 1 : 1;

        $itemCode = 'ITM-' . str_pad($number, 5, '0', STR_PAD_LEFT);

        return new Item([
            'item_code' => $itemCode,
            'name'      => $row['name'],
            'cat_id'    => $row['category_id'],
            'unit_id'   => $row['unit_id'],
            'store_id'  => $row['store_id'],
            'status'    => $row['status'],
        ]);
    }
}