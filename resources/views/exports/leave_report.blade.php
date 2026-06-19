<table class="table table-bordered text-center align-middle">
    <thead class="table-light">
        <tr>
            <th>Leave Type</th>
            <th>Entitlement</th>
            @for ($m = 1; $m <= 12; $m++)
                {{-- show each month name (Jan, Feb, … Dec) --}}
                <th>{{ \Carbon\Carbon::create()->month($m)->shortMonthName }}</th>
            @endfor
            <th>Total</th>
            <th>Leave Balance</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($leaveTypes as $lt)
            @php
                $rowTotal = 0;

                // Normalize type name whether $lt is a model/object or a plain string
                $typeName = is_object($lt) ? $lt->name ?? (string) $lt : (string) $lt;

                // get entitlement for this leave type (from controller)
                $finalEntitlement = $finalEntitlements[$typeName] ?? 0;
            @endphp
            <tr>
                <td class="text-start">{{ ucwords(str_replace('_', ' ', $typeName)) }}</td>
                {{-- text-start:align to left --}}

                {{-- show entitlement days (already prorated/full) --}}
                <td>{{ number_format($finalEntitlement - $rowTotal, 2) }}</td>

                {{-- loop through all 12 months --}}
                @for ($m = 1; $m <= 12; $m++)
                    @php
                        // number of leave applications in this month for this type
                        $count = $reportData[$typeName][$m] ?? 0;

                        // add to row total (total taken for this leave type)
                        $rowTotal += $count;
                    @endphp
                    <td>{{ $count }}</td>
                @endfor
                {{-- total leave taken for this type --}}
                <td class="fw-bold">{{ $rowTotal }}</td>

                {{-- leave balance = entitlement - total taken --}}
                <td class="fw-bold {{ $finalEntitlement - $rowTotal < 0 ? 'text-danger' : '' }}">
                    {{ number_format($finalEntitlement - $rowTotal, 2) }}
                </td>
                {{-- negative balance in red --}}
            </tr>
        @endforeach

        @php
            // --------------------------
            // Calculate Totals Row
            // --------------------------
            // Monthly totals across ALL leave types

            // first initialise monthly and grand totals
            $monthlyTotals = array_fill(1, 12, 0);

            // grand total across all leave types
            $grandTotal = 0;
            foreach ($reportData as $type => $months) {
                foreach ($months as $m => $cnt) {
                    $monthlyTotals[$m] += $cnt;
                    $grandTotal += $cnt;
                }
            }
        @endphp

        {{-- last row --}}
        <tr class="fw-bold table-secondary">
            <td class="text-center" colspan="2">Total</td>
            {{-- colspan:merge columns --}}

            {{-- monthly totals across all types --}}
            @for ($m = 1; $m <= 12; $m++)
                <td>{{ $monthlyTotals[$m] }}</td>
            @endfor

            {{-- grand total (all leave types, all months) --}}
            <td>{{ $grandTotal }}</td>

            {{-- no leave balance here because it's per type, not overall --}}
            <td>-</td>
        </tr>
    </tbody>
</table>
