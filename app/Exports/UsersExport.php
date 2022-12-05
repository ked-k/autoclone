<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithMapping, WithHeadings
{
    use Exportable;

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
        return User::with('laboratory', 'designation')->latest()->get();
    }

    public function map($user): array
    {
        $this->count++;

        return [
            $this->count,
            $user->fullName,
            $user->name,
            $user->laboratory->laboratory_name,
            $user->designation->name,
            $user->email,
            $user->contact,
            $user->is_active === 1 ? 'Active' : 'Suspended',
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Name',
            'UserName',
            'Laboratory',
            'Designation',
            'Email',
            'Contact',
            'Status',

        ];
    }
}
