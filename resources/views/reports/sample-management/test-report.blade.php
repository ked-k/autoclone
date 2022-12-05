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

            img * {
                visibility: visible;
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

            /* #bcode {
                position: fixed;
                bottom: 0;
            } */

            table,
            table tr td,
            table tr th {
                page-break-inside: avoid;
            }
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
                    <a class="navbar-brand"
                        href="
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
                                        {{ $testResult->tracker }}</span></h5>
                            </div>
                            <div class="col-12 col-lg-6 text-md-end no-print">
                                <a href="{{ route('result-report', $testResult->id) }}"
                                    class="btn btn-sm btn-info me-2"><i class="bi bi-download"></i> Download</a>
                                <a href="javascript:;" onclick="window.print()" class="btn btn-sm btn-success"><i
                                        class="bi bi-printer-fill"></i> Print</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-header text-center text-danger">
                        <img src="{{ asset('autolab-assets/images/headers/header.png') }}"
                            alt="Makerere University Logo" width="100%" height="200px">
                        <h5 class="mb-0">Result Report<span class="text-info"></h5>
                    </div>

                    <div class="card-header py-2 bg-light">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 60%">
                                        PARTICIPANT
                                    </th>
                                    <th>
                                        REQUESTER
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <strong class="text-inverse">Lab No:
                                        </strong>{{ $testResult->sample->lab_no }}<br>
                                        <strong class="text-inverse">Participant ID:
                                        </strong>
                                        <a href="{{ URL::signedRoute('participant-search-results', ['participant' => $testResult->sample->participant->id]) }}"
                                            class="text-secondary"
                                            target="_blank">{{ $testResult->sample->participant->identity }}
                                        </a><br>
                                        <strong class="text-inverse">Sample ID:
                                        </strong>
                                        <a href="{{ URL::signedRoute('sample-search-results', ['sample' => $testResult->sample->id]) }}"
                                            class="text-secondary"
                                            target="_blank">{{ $testResult->sample->sample_identity }}
                                        </a><br>
                                        <strong class="text-inverse">Name:
                                        </strong>{{ $testResult->sample->participant->surname ?? 'N/A' }}<br>
                                        <strong class="text-inverse">Age:
                                        </strong>{{ $testResult->sample->participant->age }}<b>
                                            Gender: </b>{{ $testResult->sample->participant->gender }}<br>
                                        <strong class="text-inverse">Study Name:
                                        </strong>{{ $testResult->sample->study->name ?? 'N/A' }}<br>
                                    </td>
                                    <td>
                                        <strong class="text-inverse">Name:
                                        </strong>{{ $testResult->sample->requester->name }}<br>
                                        <strong class="text-inverse">Telephone:
                                        </strong>{{ $testResult->sample->requester->contact }}<br>
                                        <strong class="text-inverse">Email:
                                        </strong>{{ $testResult->sample->requester->email }}<br>
                                        <strong class="text-inverse">Date Requested:
                                        </strong>{{ date('d-m-Y', strtotime($testResult->sample->date_requested)) }}<br>
                                        <strong class="text-inverse">Organisation: </strong>
                                        {{ $testResult->sample->requester->facility->name }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td colspan="2">
                                            <strong class="text-inverse">Test Requested:
                                            </strong>{{ $testResult->test->name }}
                                        </td>
                                        <td>
                                            <strong class="text-inverse">Sample Type:
                                            </strong>{{ $testResult->sample->sampleType->type }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="">
                                            <strong class="text-inverse">Collection Date:
                                            </strong>{{ date('d-m-Y', strtotime($testResult->sample->date_collected)) }}
                                        </td>
                                        <td>
                                            <strong class="text-inverse">Date Received:
                                            </strong>{{ date('d-m-Y H:i', strtotime($testResult->sample->sampleReception->date_delivered)) }}
                                        </td>
                                        <td>
                                            <strong class="text-inverse">Result Date:
                                            </strong>{{ $testResult->created_at }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            <strong class="text-inverse">Result:
                                            </strong>
                                            @if ($testResult->result)
                                                {{ $testResult->result }}
                                            @else
                                                <a href="{{ route('attachment.download', $testResult->id) }}">See
                                                    Attachment</a>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row bg-light align-items-center m-0">
                            <strong class="text-inverse">Comments:</strong>
                            <p>{{ $testResult->comment }}</p>
                        </div>
                        <!--end row-->

                        <hr>
                        <div class="my-3">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>
                                            @if ($testResult->performer->signature)
                                                <img src="{{ asset('storage/' . $testResult->performer->signature) }}"
                                                    alt="" height="5%" width="30%"><br>
                                            @endif
                                            <hr>
                                            <strong>Performed By</strong><br>

                                            {{ $testResult->performer ? $testResult->performer->fullName : 'N/A' }}
                                        </td>
                                        <td>
                                            @if ($testResult->reviewer->signature)
                                                <img src="{{ asset('storage/' . $testResult->reviewer->signature) }}"
                                                    alt="" height="5%" width="30%"><br>
                                            @endif
                                            <hr>
                                            <strong>Rewiewed By</strong><br>

                                            {{ $testResult->reviewer->fullName }}
                                        </td>
                                        <td>
                                            @if ($testResult->approver->signature)
                                                <img src="{{ asset('storage/' . $testResult->approver->signature) }}"
                                                    alt="" height="5%" width="30%"><br>
                                            @endif
                                            <hr>
                                            <strong>Approved By</strong><br>

                                            {{ $testResult->approver->fullName }}
                                        </td>
                                        <td>
                                            {{ QrCode::size(84)->generate(
                                                $testResult->tracker .
                                                    '|' .
                                                    $testResult->sample->participant->identity .
                                                    '|' .
                                                    $testResult->sample->sample_identity,
                                            ) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer py-3">
                    <p class="text-center mb-2">
                        The Laboratory is Certified by the Ministry of Health Uganda
                    </p>
                </div>
            </div>
    </div>
    </main>
    </div>


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
