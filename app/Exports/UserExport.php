<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Carbon\Carbon;

class UserExport implements FromQuery, WithHeadings, WithTitle
{
    use Exportable;

    protected $date;
    protected $chunkSize = 1000; // Default chunk size

    public function __construct($date)
    {
        $this->date = Carbon::parse($date);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        return User::query()
            ->where('created_at', '<', $this->date)
            ->select('*');
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Name',
            'Email'
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
