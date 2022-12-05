<?php

namespace App\Exports;

use App\Models\asset\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AssetsExport implements FromCollection, WithMapping, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public $count;

    public function __construct()
    {
        $this->count = 0;
    }

    public function collection()
    {
        return Asset::with('category:id,category_name', 'subcategory:id,subcategory_name', 'user:id,name', 'vendor:id,vendor_name', 'station:id,station_name',
            'department:id,department_name', 'insurer:id,vendor_name', 'insurancetype:id,type')->orderBy('created_at', 'desc')->get();
    }

    public function map($asset): array
    {
        $this->count++;
        if ($asset->user == null) {
            $user = 'N/A';
        } else {
            $user = $asset->user->name;
        }
        if ($asset->vendor == null) {
            $vendor = 'N/A';
        } else {
            $vendor = $asset->vendor->vendor_name;
        }
        if ($asset->insurer == null) {
            $insurer = 'N/A';
        } else {
            $insurer = $asset->insurer->vendor_name;
        }
        if ($asset->insurancetype == null) {
            $type = 'N/A';
        } else {
            $type = $asset->insurancetype->type;
        }

        return [
            $this->count,
            $asset->asset_name,
            $asset->category->category_name,
            $asset->subcategory->subcategory_name,
            $asset->brand,
            $asset->model,
            $asset->serial_number,
            $asset->barcode,
            $asset->engraved_label,
            $asset->status,
            $user,
            $asset->station->station_name,
            $asset->department->department_name,
            $asset->condition,
            $vendor,
            $asset->purchase_price,
            $asset->purchase_date,
            $asset->purchase_order_number,
            $asset->warranty_end,
            $asset->depreciation_method,
            $asset->depreciation_rate,
            $asset->expected_useful_years,
            $insurer,
            $type,
            $asset->insurance_end,
            $asset->remarks,
            // Carbon::parse($asset->event_date)->toFormattedDateString(),
            // Carbon::parse($asset->created_at)->toFormattedDateString()
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Name',
            'Category',
            'Subcategory',
            'Brand',
            'Model',
            'Serial Number',
            'Barcode',
            'Engravement',
            'Status',
            'Assigned To',
            'Station',
            'Department',
            'Condition',
            'Vendor',
            'Purchase Price',
            'Purchase Date',
            'LPO Number',
            'Warranty End',
            'Depreciation Method',
            'Depreciation Rate',
            'Expected Useful Years',
            'Insurance Company',
            'Insurance Type',
            'Insurance End',
            'Remarks',
        ];
    }
}
