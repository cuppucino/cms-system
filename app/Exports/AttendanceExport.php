<?php

namespace App\Exports;

use App\Models\AttendanceRecord;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return AttendanceRecord::with(['user', 'session'])
            ->get()
            ->map(function ($record) {
                return [
                    'ID' => $record->id,
                    'Student Name' => $record->user->name,
                    'Session' => $record->session->name ?? '-',
                    'Status' => $record->status,
                    'Checked In At' => $record->checked_in_at,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Student Name',
            'Session',
            'Status',
            'Checked In At',
        ];
    }
}
