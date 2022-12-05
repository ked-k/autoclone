<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="'minimal-theme'}}">


<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <title>{{ config('app.name', 'AutoLab') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('autolab-assets/images/favicon-32x32.png') }}" type="image/png" />
    <!-- Bootstrap CSS -->
    <link href="{{ asset('autolab-assets/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('autolab-assets/css/bootstrap-extended.css') }}" rel="stylesheet" />
    <link href="{{ asset('autolab-assets/css/style.css') }}" rel="stylesheet" />
    <link href="{{ asset('autolab-assets/css/icons.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

    <!-- loader-->
    <link href="{{ asset('autolab-assets/css/pace.min.css') }}" rel="stylesheet" />

    <title>Search Results</title>
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
</head>

<body class="bg-surface">

    <!--start wrapper-->
    <div class="wrapper">
        <header class="no-print">
            <nav class="navbar navbar-expand-lg navbar-light bg-white rounded-0 border-bottom">
                <div class="container">
                    <a class="navbar-brand" href="
                @if (Auth::user()->hasPermission(['manager-access'])) {{ route('manager-dashboard') }}
                @elseif (Auth::user()->hasPermission(['master-access']))
                {{ route('master-dashboard') }}
                @elseif (Auth::user()->hasPermission(['normal-access']))
                {{ route('user-dashboard') }} @endif
                "><img
                            src="{{ asset('autolab-assets/images/brand-logo-2.png') }}" width="140"
                            alt="" /></a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                            <li class="nav-item">

                            </li>
                        </ul>
                        <div class="d-flex ms-3 gap-3">
                            <a href="
                @if (Auth::user()->hasPermission(['manager-access'])) {{ route('manager-dashboard') }}
                @elseif (Auth::user()->hasPermission(['master-access']))
                {{ route('master-dashboard') }}
                @elseif (Auth::user()->hasPermission(['normal-access']))
                {{ route('user-dashboard') }} @endif
                "
                                class="btn btn-outline-info btn-sm px-4 radius-30">Dashboard</a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="javascript:;" onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="dropdown-item btn btn-outline-secondary btn-sm px-4 radius-30">
                                    <div class="d-flex align-items-center">
                                        <div class="setting-icon"><i class="bi bi-lock-fill"></i></div>
                                        <div class="setting-text ms-3"><span>Logout</span></div>
                                    </div>
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>
        </header>

        <!--start content-->
        <main id="reportContainer">
            <div class="container card mt-2">
                <div>
                    <div class="card-header py-3 no-print">
                        <div class="row align-items-center g-3">
                            <div class="col-12 col-lg-6">
                                <h5 class="mb-0"><span class="text-info">
                                        {{ $sampleReception->batch_no }}</span></h5>
                            </div>
                            <div class="col-12 col-lg-6 text-md-end no-print">
                                <a href="javascript:;" onclick="window.print()" class="btn btn-sm btn-success"><i
                                        class="bi bi-printer-fill"></i> Print</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-header text-center text-danger">
                        <img src="{{ asset('autolab-assets/images/headers/header.png') }}"
                            alt="Makerere University Logo" width="100%" height="200px">
                        <h5 class="mb-0">{{$sampleReception->batch_no}}<span class="text-info"> Details</h5>
                    </div>
                        <div class="card-header py-2 bg-light">
                            <div class="row">
                                <div class="table-responsiv col">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>{{ Str::upper(auth()->user()->laboratory->laboratory_name) }}</th>
                                                <th>SOURCE FACILITY</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <strong class="text-inverse">Received By: </strong>
                                                    {{ $sampleReception->receiver->fullName }}<br>
                                                    <strong class="text-inverse">Samples Delivered:
                                                    </strong>{{ $sampleReception->samples_delivered ?? 'N/A' }}<br>
                                                    <strong class="text-inverse">Samples Accepted:
                                                    </strong>{{ $sampleReception->samples_accepted ?? 'N/A' }}<br>
                                                    <strong class="text-danger">Samples Rejected:
                                                    </strong>{{ $sampleReception->samples_rejected ?? 'N/A' }}<br>
                                                    <strong class="text-inverse">Reviewed by:
                                                    </strong>{{ $sampleReception->reviewer->fullName }}<br>
                                                    <strong class="text-inverse">Date Reviewed:
                                                    </strong>{{ date('d-m-Y', strtotime($sampleReception->created_at)) }}<br>
                                                </td>
                                                <td>
                                                    <strong class="text-inverse">Name: </strong>
                                                    {{ $sampleReception->facility->name }}<br>
                                                    <strong class="text-inverse">Courier Name:
                                                    </strong>{{ $sampleReception->courier->name ?? 'N/A' }}<br>
                                                    <strong class="text-inverse">Courier Telephone:
                                                    </strong>{{ $sampleReception->courier->contact ?? 'N/A' }}<br>
                                                    <strong class="text-inverse">Courier Email:
                                                    </strong>{{ $sampleReception->courier->email ?? 'N/A' }}<br>
                                                    <strong class="text-inverse">Date Delivered:
                                                    </strong>{{ date('d-m-Y H:i', strtotime($sampleReception->date_delivered)) }}<br>
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            @if (!$sampleReception->sample->isEmpty())
                                <div>
                                    <table class="table table-striped mb-0 w-100 ">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Participant ID</th>
                                                <th>Sample</th>
                                                <th>Study</th>
                                                <th>Sample ID</th>
                                                <th>Lab_No</th>
                                                <th>Test Count</th>
                                                <th>Requested By</th>
                                                <th>Collected By</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($sampleReception->sample as $key => $sample)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>
                                                        <a href="{{ URL::signedRoute('participant-search-results', ['participant' => $sample->participant->id]) }}"
                                                            class="text-secondary"
                                                            target="_blank">{{ $sample->participant->identity }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        {{ $sample->sampleType->type }}
                                                    </td>
                                                    <td>
                                                        {{ $sample->study->name ?? 'N/A' }}
                                                    </td>
                                                    <td>
                                                        <a href="{{ URL::signedRoute('sample-search-results', ['sample' => $sample->id]) }}"
                                                            class="text-secondary"
                                                            target="_blank">{{ $sample->sample_identity }}
                                                        </a>

                                                    </td>
                                                    <td>
                                                        {{ $sample->lab_no ?? 'N/A' }}
                                                    </td>
                                                    <td>
                                                        {{ $sample->test_count ?? 'N/A' }}
                                                    </td>
                                                    <td>
                                                        {{ $sample->requester->name }}
                                                    </td>
                                                    <td>
                                                        {{ $sample->collector->name ?? 'N/A' }}
                                                    </td>
                                                </tr>
                                            @empty
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div> <!-- end preview-->
                            @endif

                            @if ($sampleReception->comment)
                                <div class="row bg-light align-items-center m-0">
                                    <strong class="text-inverse">Comments:</strong>
                                    <p>{{ $sampleReception->comment }}</p>
                                </div>
                                <hr>
                            @endif
                        </div>

                        <div class="card-footer py-3 me-3 float-end">
                            {{ QrCode::size(84)->generate($sampleReception->batch_no.'|'.$sampleReception->samples_accepted)}}
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!--end page main-->

        <footer class="bg-white border-top p-3 text-center fixed-bottom">
            <p class="mb-0">Makerere University Biomedical Research Centre © {{ date('Y') }}. All right
                reserved.
            </p>
        </footer>

    </div>
    <!--end wrapper-->

    <!-- Bootstrap bundle JS -->
    <script src="{{ asset('autolab-assets/js/bootstrap.bundle.min.js') }}"></script>
    <!--plugins-->
    <script src="{{ asset('autolab-assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('autolab-assets/js/pace.min.js') }}"></script>

</body>

</html>
