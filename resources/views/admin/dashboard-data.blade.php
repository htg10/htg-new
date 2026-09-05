@if (
    (isset($income) && count($income) > 0) ||
    (isset($expense) && count($expense) > 0)
)
    <div class="htg-totals mb-3">
        <div>
            <span>Total Income</span>
            <strong style="color:var(--htg-ok)">
                <span class="htg-cur">₹</span>{{ number_format($totalIncome ?? 0, 2) }}
            </strong>
        </div>
        <div>
            <span>Total Expense</span>
            <strong style="color:var(--htg-bad)">
                <span class="htg-cur">₹</span>{{ number_format($totalExpense ?? 0, 2) }}
            </strong>
        </div>
        <div>
            <span>Net Balance</span>
            <strong style="color:{{ ($netBalance ?? 0) >= 0 ? 'var(--htg-ok)' : 'var(--htg-bad)' }}">
                <span class="htg-cur">₹</span>{{ number_format($netBalance ?? 0, 2) }}
            </strong>
        </div>
        <div>
            <span>Records</span>
            <strong>{{ count($income ?? []) + count($expense ?? []) }}</strong>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Type</th>
                    <th>Payment Mode</th>
                    <th>Date</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>

            <tbody>
                {{-- INCOME --}}
                @foreach ($income ?? [] as $row)
                    <tr class="table-success">
                        <td>
                            <strong>Income</strong>
                        </td>
                        <td>{{ $row->payment ?? '-' }}</td>
                        <td class="htg-fig">
                            {{ !empty($row->date) ? \Carbon\Carbon::parse($row->date)->format('d-m-Y') : '-' }}
                        </td>
                        <td class="text-end htg-fig htg-strong">
                            <span class="htg-cur">₹</span>{{ number_format($row->amount ?? 0, 2) }}
                        </td>
                    </tr>
                @endforeach

                {{-- EXPENSE --}}
                @foreach ($expense ?? [] as $row)
                    <tr class="table-danger">
                        <td>
                            <strong>Expense</strong>
                        </td>
                        <td>{{ $row->payment ?? '-' }}</td>
                        <td class="htg-fig">
                            {{ !empty($row->date) ? \Carbon\Carbon::parse($row->date)->format('d-m-Y') : '-' }}
                        </td>
                        <td class="text-end htg-fig htg-strong">
                            <span class="htg-cur">₹</span>{{ number_format($row->amount ?? 0, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot class="table-light">
                <tr>
                    <th colspan="3" class="text-end">Total Income</th>
                    <th class="text-end htg-fig" style="color:var(--htg-ok)">
                        <span class="htg-cur">₹</span>{{ number_format($totalIncome ?? 0, 2) }}
                    </th>
                </tr>

                <tr>
                    <th colspan="3" class="text-end">Total Expense</th>
                    <th class="text-end htg-fig" style="color:var(--htg-bad)">
                        <span class="htg-cur">₹</span>{{ number_format($totalExpense ?? 0, 2) }}
                    </th>
                </tr>

                <tr>
                    <th colspan="3" class="text-end">Net Balance</th>
                    <th class="text-end htg-fig"
                        style="color:{{ ($netBalance ?? 0) >= 0 ? 'var(--htg-ok)' : 'var(--htg-bad)' }}">
                        <span class="htg-cur">₹</span>{{ number_format($netBalance ?? 0, 2) }}
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
@else
    <div class="htg-empty">
        <i class="bx bx-search-alt"></i>
        <p>
            <strong>No records match this filter</strong>
            Widen the date range or choose a different payment mode.
        </p>
    </div>
@endif
