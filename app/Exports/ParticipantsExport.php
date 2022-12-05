<?php

namespace App\Exports;

use App\Models\Participant;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithProperties;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ParticipantsExport implements FromCollection, WithMapping, WithHeadings,WithStyles,WithProperties
{
    use Exportable;

    /**
     * @return \Illuminate\Support\Collection
     */
    public $count;

    public $participantIds;

    public function __construct($participantIds)
    {
        $this->count = 0;
        $this->participantIds = $participantIds;
    }

    public function properties(): array
    {
        return [
            'creator'        => auth()->user()->fullName,
            'lastModifiedBy' => 'Autolab',
            'title'          => 'Participants',
            'description'    => 'Participants export',
            'subject'        => 'Participants export',
            'keywords'       => 'Autolab exports',
            'category'       => 'Autolab Exports',
            'manager'        => 'MakBRC IT TEAM',
            'company'        => 'Makerere University Biomedical Research Centre',
        ];
    }

    public function collection()
    {
        return Participant::whereIn('id', $this->participantIds)->with('facility', 'study')->latest()->get();
    }

    public function map($participant): array
    {
        $this->count++;

        return [
            $this->count,
            $participant->participant_no,
            $participant->identity ?? 'N/A',
            $participant->age ?? 'N/A',
            $participant->gender ?? 'N/A',
            $participant->contact ?? 'N/A',
            $participant->address ?? 'N/A',
            $participant->nok_contact ?? 'N/A',
            $participant->nok_address ?? 'N/A',
            $participant->clinical_notes ?? 'N/A',
            $participant->title ?? 'N/A',
            $participant->nin_number ?? 'N/A',
            $participant->surname ?? 'N/A',
            $participant->first_name ?? 'N/A',
            $participant->other_name ?? 'N/A',
            $participant->nationality ?? 'N/A',
            $participant->district ?? 'N/A',
            $participant->dob ?? 'N/A',
            $participant->email ?? 'N/A',
            $participant->birth_place ?? 'N/A',
            $participant->religious_affiliation ?? 'N/A',
            $participant->occupation ?? 'N/A',
            $participant->civil_status ?? 'N/A',
            $participant->nok ?? 'N/A',
            $participant->nok_relationship ?? 'N/A',
            $participant->facility->name ?? 'N/A',
            $participant->study->name ?? 'N/A',
            $participant->entry_type ?? 'N/A',
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Participant_no',
            'Identity',
            'Age',
            'Gender',
            'Contact',
            'Address',
            'nok_contact',
            'nok_address',
            'Clinical_notes',
            'Title',
            'NIN_number',
            'Surname',
            'First_name',
            'Other_name',
            'Nationality',
            'District',
            'DOB',
            'Email',
            'Birth_place',
            'Religious_affiliation',
            'Occupation',
            'Civil_status',
            'Nok',
            'NOK relationship',
            'Facility',
            'Study Name',
            'Entry Type',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }
}
