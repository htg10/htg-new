<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PaymentExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = DB::table('entries as e')
            ->join('products as p', 'p.entry_id', '=', 'e.id')
            ->select(
                'e.payment as Bank',
                DB::raw('SUM(p.paid_amount) as Amount')
            )
            ->whereNotNull('e.payment');

        if ($this->request->payment_mode) {
            $query->where('e.payment', $this->request->payment_mode);
        }

        if ($this->request->from_date && $this->request->to_date) {
            $query->whereBetween('e.date', [
                $this->request->from_date,
                $this->request->to_date
            ]);
        }

        return $query->groupBy('e.payment')->get();
    }

    public function headings(): array
    {
        return ['Bank Name', 'Total Amount'];
    }
}
