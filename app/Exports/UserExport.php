<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;

class UserExport implements FromCollection, WithHeadings, WithTitle
{
    protected $date;

    public function __construct($date)
    {
        $this->date = Carbon::parse($date);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::where('created_at', '<', $this->date)->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Email Verified At',
            'Created At',
            'Updated At'
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Users List';
    }
}
