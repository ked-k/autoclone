<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="minimal-theme">


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

            /* table,
            table tr td,
            table tr th {
                page-break-inside: avoid;
            } */
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
                <div class="card-header py-3 no-print">
                    <div class="row align-items-center g-3">
                        <div class="col-12 col-lg-6">
                            <h5 class="mb-0"><span class="text-danger">
                                    Combined </span>Test Result Report</h5>
                        </div>
                        <div class="col-12 col-lg-6 text-md-end no-print">
                            <a href="javascript:;" onclick="window.print()" class="btn btn-sm btn-success"><i
                                    class="bi bi-printer-fill"></i> Print</a>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="card-header text-center text-danger">
                        <img src="{{ asset('autolab-assets/images/headers/header.png') }}"
                            alt="Makerere University Logo" width="100%" height="200px">
                        <h5> {{ Str::upper(auth()->user()->laboratory->laboratory_name) }}</h5>
                        <table class="table mb-0 w-100">
                            <tbody>
                                <tr class="text-center">
                                    <td>
                                        <strong class="text-inverse">FACILITY:
                                        </strong>
                                        {{ $samples[1]->participant->facility->name ?? 'N/A' }}<br>
                                    </td>
                                    <td>
                                        <strong class="text-inverse">STUDY: </strong>
                                        {{ $samples[1]->study->name }}
                                    </td>
                                    <td>
                                        <strong class="text-inverse">REQUESTER:
                                        </strong>{{ $samples[1]->requester->name ?? 'N/A' }}<br>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                    <div class="card-body">
                        @forelse ($samples as $sample)
                            <div class="row">
                                <div class="col">
                                    <table class="table table-striped mb-0 w-100">
                                        <tr>
                                            <th colspan="3">SAMPLE <strong
                                                    class="text-info">{{ $sample->sample_identity }}</strong> (
                                                <strong class="text-inverse">Participant ID:
                                                </strong><span
                                                    class="text-info">{{ $sample->participant->identity ?? 'N/A' }}
                                                </span><strong class="text-inverse"> | Visit:
                                                </strong>{{ $sample->visit ?? 'N/A' }})
                                            </th>
                                        </tr>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <strong class="text-inverse">Sample Type:
                                                    </strong>{{ $sample->sampleType->type ?? 'N/A' }}<br>
                                                    <strong class="text-success">Lab No:
                                                    </strong>{{ $sample->lab_no }}
                                                </td>
                                                <td>
                                                    <strong class="text-inverse">Requested By:
                                                    </strong>{{ $sample->requester->name ?? 'N/A' }}<br>
                                                    <strong class="text-inverse">Collected By:
                                                    </strong>{{ $sample->collector->name ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    <strong class="text-inverse">Date collected:
                                                    </strong>{{ date('d-m-Y H:i', strtotime($sample->date_collected)) }}<br>
                                                    <strong class="text-inverse">Date Received:
                                                    </strong>{{ date('d-m-Y H:i', strtotime($sample->sampleReception->date_delivered)) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div>
                                <table class="table mb-0 w-100">
                                    <tr>
                                        <th>Test Requested</th>
                                        <th>Result</th>
                                        <th>Result Date</th>
                                        <th>Performed By</th>
                                        <th>Reviewed By</th>
                                        <th>Approved By</th>
                                    </tr>
                                    <tbody>
                                        @forelse ($sample->testResult as $result)
                                            <tr>
                                                <td>
                                                    <strong class="text-success">{{ $result->test->name }}</strong>
                                                </td>
                                                <td>
                                                    @if ($result->result)
                                                        {{ $result->result }}<br>
                                                        @if ($result->comment)
                                                            <strong class="text-inverse">Comments:</strong>
                                                            <p>{{ $result->comment }}</p>
                                                        @endif
                                                    @else
                                                        <a href="{{ route('attachment.download', $result->id) }}">See
                                                            Attachment</a>
                                                    @endif
                                                </td>
                                                <td>
                                                    </strong>{{ date('d-m-Y H:i', strtotime($result->created_at)) }}
                                                </td>
                                                <td>

                                                    {{ $result->performer ? $result->performer->fullName : 'N/A' }}
                                                </td>
                                                <td>
                                                    {{ $result->performer ? $result->reviewer->fullName : 'N/A' }}
                                                </td>
                                                <td>
                                                    {{ $result->performer ? $result->approver->fullName : 'N/A' }}
                                                </td>
                                            </tr>
                                        @empty
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @empty

                        @endforelse
                    </div>

                    <div class="card-footer py-3 me-3 float-end">
                        {{ QrCode::size(84)->generate($qrCodeContent)}}
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
