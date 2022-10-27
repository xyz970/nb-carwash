<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;

class TransactionExport implements FromCollection
{
    private $startDate;
    private $endDate;
    public function __construct($startDate = '', $endDate = '') {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        if (empty($this->startDate) && empty($this->endDate)) {
            return Transaction::all();            
        }else{
            return Transaction::with('washType')->whereBetween('date',[$this->startDate,$this->endDate])->get();
        }
    }
}
