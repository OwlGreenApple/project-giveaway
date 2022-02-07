<?php

namespace App\Exports;

use App\Models\Contestants;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ContestantExport implements FromQuery, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public $evid;
    public function __construct($evid)
    {
        $this->ev_id = $evid;
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'WA Number',
            'Total Entries',
            'Total Referrals',
            'Date Enter',
        ];
    }

    public function query()
    {
        return Contestants::query()->where('event_id',$this->ev_id)
        ->select(['c_name','c_email','wa_number','entries','referrals','date_enter'])
        ->orderBy('id','asc');
    }
}
