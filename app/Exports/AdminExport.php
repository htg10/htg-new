<?php

namespace App\Exports;

use App\Models\Entry;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AdminExport implements FromCollection, WithMapping, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $request;

    // Constructor to accept filters
    public function __construct($request = null)
    {
        $this->request = $request;
    }
    // public function collection()
    // {
    //     $query = Entry::with('product');

    //     if (!empty($this->request)) {
    //         if ($this->request === 'Expired') {
    //             $query->whereHas('product', function ($query) {
    //                 $query->where('expiry_date', '<', now());
    //             });
    //         } elseif ($this->request === 'Pending') {
    //             $query->whereHas('product', function ($query) {
    //                 $query->whereRaw('total_amount - paid_amount > 0');
    //             });
    //         }
    //     }

    //     return $query->get();
    // }

    public function collection()
    {
        $query = Entry::with('product');

        // Company filter
        if ($this->request->company) {
            $query->where('company', 'LIKE', '%' . $this->request->company . '%');
        }

        // Year filter
        if ($this->request->year) {
            $query->whereYear('date', $this->request->year);
        }

        // Month filter
        if ($this->request->month) {
            $query->whereMonth('date', $this->request->month);
        }

        // Status filter
        if ($this->request->inputState === 'Expired') {
            $query->whereHas('product', function ($q) {
                $q->where('expiry_date', '<', now());
            });
        }

        if ($this->request->inputState === 'Pending') {
            $query->whereHas('product', function ($q) {
                $q->whereRaw('total_amount - paid_amount > 0');
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return ['ID', 'Company Name', 'Type', 'Email', 'Contact Number', 'Total Amount', 'Balance Payment', 'GST', 'Address', 'Date'];
    }

    public function map($entry): array
    {
        static $index = 1;

        $totalAmount = $entry->product?->sum('total_amount') ?? 0;
        $paidAmount = $entry->product?->sum('paid_amount') ?? 0;

        return [
            $index++,
            $entry->company,
            $entry->type,
            $entry->email,
            $entry->contactno,
            $totalAmount,
            $totalAmount - $paidAmount,
            $entry->gst,
            $entry->address,
            $entry->created_at->format('Y-m-d'),
        ];
    }

    // public function map($entry): array
    // {
    //     $totalAmount = $entry->product ? $entry->product->sum('total_amount') : 0;
    //     $balancePayment = $totalAmount - ($entry->product ? $entry->product->sum('paid_amount') : 0);

    //     static $index = 1;

    //     return [
    //         $index++,
    //         $entry->company ?? '',
    //         $entry->type ?? '',
    //         $entry->email ?? '',
    //         $entry->contactno ?? '',
    //         $totalAmount ?? '',
    //         $balancePayment ?? '',
    //         $entry->gst ?? '',
    //         $entry->address ?? '',
    //         $entry->created_at->format('Y-m-d'),
    //     ];
    // }
}
