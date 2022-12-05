<style type="text/css">
    @media print {
        body * {
            visibility: hidden;
        }


        #reportContainer,
        #reportContainer * {
            visibility: visible;
        }

        #reportContainer {
            position: static;
            /* overflow: auto; */
            left: 0;
            top: 0;
        }

        .no-print,
        .no-print * {
            display: none !important;
        }

        #noprint1 {
            visibility: hidden;
        }

        #bcode {
            position: fixed;
            bottom: 0;
        }

        /* table{
            border-collapse:collapse;
        } */
        /* tr td{
            page-break-inside: avoid;
            white-space: nowrap;
        } */
        /* table tbody tr td:before,
        table tbody tr td:after {
            content: "";
            height: 4px;
            display: block;
        } */

        /* table.table-bordered tr td th {
            page-break-inside: avoid;
        } */

        table,
        table tr td,
        table tr th {
            page-break-inside: avoid;
        }

        /* 
            table { page-break-after:auto }
            tr    { page-break-inside:avoid; page-break-after:auto }
            td    { page-break-inside:avoid; page-break-after:auto }
            thead { display:table-header-group }
            tfoot { display:table-footer-group }
        */
    }

    th * {
        line-height: 20px;
        min-height: 5px;
        height: 5px;
    }
</style>
<!-- start page title -->
<x-page-title>
    {{ $pagetitle }}
</x-page-title> 

<div class="card">
    <div class="card-body">
        <div class="row mt-0" id="reportContainer">
            <div class="col-lg-12">
                <table class="table table-centered mb-0" id="reportHeader">
                    <tbody>
                        <tr>
                            <td style="width: 10%">
                                <img class="d-flex align-self-end rounded me-0"
                                    src="{{ asset('storage/' . $facilityInfo->logo2) }}" alt="logo" height="90"
                                    width="100">
                            </td>
                            <td class="text-center">
                                <div class="w-100 overflow-hidde">
                                    <h4 class="mt-1 mb-0">{{ $facilityInfo->facility_name }}</h4>
                                    <p class="mb-1 mt-1 text-mute">{{ $facilityInfo->address2 }}
                                        <span> || {{ $facilityInfo->physical_address}}</span> <br>
                                        <span><strong>Tel:</strong> {{ $facilityInfo->contact }}
                                            @if ($facilityInfo->contact2)
                                            /{{ $facilityInfo->contact2 }}
                                            @endif
                                        </span>
                                        ||
                                        @if ($facilityInfo->fax)
                                            <span><strong>Fax:</strong> {{ $facilityInfo->fax }}</span> <br>
                                        @endif
                                        @if ($facilityInfo->email)
                                            <span><strong>Email:</strong> {{ $facilityInfo->email }}</span>
                                        @endif
                                         ||
                                         @if ($facilityInfo->website)
                                         <span><strong>Web:</strong> {{ $facilityInfo->website }}</span>
                                         @endif
                                    </p>
                                    <h4>{{$reporttitle}}</h4>
                                </div>
                            </td>
                            <td style="width: 10%">
                                <img class="d-flex align-self-end rounded me-0"
                                    src="{{ asset('storage/' . $facilityInfo->logo) }}" alt="logo"  height="90"
                                    width="100">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            {{ $slot }}
        </div>
    </div>
</div>
