<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Test Result Report</title>
    <style>
        body {
            margin: 0;
            font-family: Nunito, sans-serif;
            font-size: .8rem;
            font-weight: 400;
            line-height: 1.5;
            color: #000000;
            background-color: #ffffff;
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: transparent
        }

        hr {
            margin: 1rem 0;
            color: inherit;
            background-color: currentColor;
            border: 0;
            opacity: .25
        }

        hr:not([size]) {
            height: 1px
        }

        a:link {
        text-decoration: none;
        color: #44a847;
        }

        a:visited {
        text-decoration: none;
        }

        a:hover {
        text-decoration: underline;
        }

        a:active {
        text-decoration: underline;
        }
        .text_centered {
            position: absolute;
            top: 56%;
            left: 6%;
            /* transform: translate(-50%, -50%); */
            color: red
        }

        table {
            border-collapse: collapse;
        }

        .btop {
            border: none;
            border-top: 1px solid #DDDDDD 1.0pt;
            mso-border-top-alt:
                solid #DDDDDD .75pt;
            mso-border-top-alt:
                solid #DDDDDD .75pt;
            mso-border-bottom-alt:
                solid #DDDDDD .75pt;
            padding-top: 5px;
            padding-bottom: 5px;
            border-block-start-style: outset;
        }

        #parameters td {
            text-align: center;
        }
    </style>
</head>

<body style="line-height:1.2; font-family:times;">
    {{-- REPORT HEADER --}}
    <div class="row">
        {{-- <img src="{{ asset('autolab-assets/images/headers/header-min.png') }}" alt="Makerere University Logo" width="100%"
            style="vertical-align:middle;"
            onerror="this.onerror=null;this.src='{{ asset('images/photos/20220130105722.jpg') }}';"> --}}
        <div style="text-align: center; line-height: 2px; border-bottom: 0.5px solid rgb(f, f, f);">
            <table width="100%" style="text-align: center; line-height: 1px; width:100%; margin-bottom:-16px">
                <tr style="padding: 0px; margin:0px">
                    <td style="text-align: right ;padding: 0px; margin:0px" width="40%">
                        <h1>MAKERERE</h1>
                    </td>
                    <td style="padding:0px; margin-top:0px ; padding-bottom: 15px; " width="10%"><img
                            src="{{ asset('autolab-assets/images/headers/logo.png') }}" alt="Makerere University Logo"
                            width="90px" style="vertical-align:middle;">
                    </td>
                    <td style="text-align: left; padding: 0px; margin:0px" width="40%">
                        <h1>UNIVERSITY</h1>
                    </td>
                </tr>
            </table>
            <h2>COLLEGE OF HEALTH SCIENCES</h2>
            <h3>School of Biomedical Sciences</h3>
            <h3>Department of Immunology and Molecular Biology</h3>
            <h3 style="color:rgb(8, 219, 131)">Genomics, Molecular and Immunology Laboratories</h2>
        </div>
        {{-- <hr style="height:0.6px; width:100%; color:#6C757D;"> --}}
        <h3 style="text-align:center; font-size:20px"><b>
                @if ($testResult->status != 'Approved')
                    <span style="color: crimson">Perliminary</span>
                @endif Result Report
                @if ($testResult->amended_state)
                (<strong style="color: crimson">AMENDED</strong>)
                @endif
            </b>
        </h3>
    </div>
    {{-- PARTICIPANT AND REQUESTER --}}
    <div style="font-size:16px; margin-top:0px;">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td style="width:50%">
                    <div>
                        <br>
                        <b>Lab No: </b>
                        <font> {{ $testResult->sample->lab_no }}</font><br>
                        <b>Participant ID: </b>{{ $testResult->sample->participant->identity }}<br> <b>Sample ID:</b>
                        {{ $testResult->sample->sample_identity }}<br>
                        <b>Name:</b> {{ $testResult->sample->participant->surname ?? 'N/A' }}<br>
                        <b>Age:</b> @if ($testResult->sample->participant->age != null) {{ $testResult->sample->participant->age}}yrs  &nbsp; @elseif ($testResult->sample->participant->months != null)
                         {{ $testResult->sample->participant->months}}months @else N/A @endif
                         <b>Gender:</b> {{ $testResult->sample->participant->gender ?? 'N/A' }}<br>
                        <b>Address:</b> {{ $testResult->sample->participant->address ?? 'N/A' }}<br>
                        <b>Study Name:</b> {{ $testResult->sample->study->name ?? 'N/A' }}<br>
                    </div>
                </td>
                <td style="width:5%"></td>
                <td style="width:45%">
                    <div>
                        <b>Requester</b> <br>
                        <b>Name:</b> {{ $testResult?->sample?->requester?->name??'N/A' ?? 'N/A' }}<br>
                        <b>Telephone:</b> {{ $testResult->sample->requester->contact ?? 'N/A' }} <br>
                        <b>Email:</b> {{ $testResult->sample->requester->email ?? 'N/A' }} <br>
                        <b>Date
                            Requested:</b>{{ date('d-m-Y', strtotime($testResult->sample->date_requested ?? 'N/A')) }}<br>
                        <b>Organisation:</b>
                        {{ $testResult->sample->requester->facility->name ?? 'N/A' }}
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="col-12 table-responsive" style="font-size:15px; margin-top:20px; text-align: center">
        <table class="table dt-responsive nowrap" width="100%">
            <tbody>
                {{-- SAMPLE AND TEST DETAILS --}}
                <tr class="btop" style="border-top: 0.5px solid rgb(f, f, f);">
                    <td class="btop">
                        <div><b>Test Requested: </b>{{ $testResult->test->name ?? 'N/A' }}<div>
                    </td>
                    <td class="btop"><strong>Sample Type: </strong>{{ $testResult->sample->sampleType->type ?? 'N/A' }}</td>
                </tr>
            </tbody>
        </table>
        <table class="table dt-responsive nowrap" width="100%">
            <tbody>
                <tr style="border-bottom: 0.5px solid rgb(f, f, f); border-top: 0.5px solid rgb(f, f, f); margin-top: 10px; margin-bottom: 10px">
                    <td class="btop" style="text-align: center"><strong>Collection Date:</strong> <br>
                        {{ $testResult->sample->date_collected ? date('d-m-Y H:i', strtotime($testResult->sample->date_collected)) : 'N/A' }}
                    </td>
                    <td class="btop" style="text-align: center"><strong>Date Received:</strong> <br>
                        {{ date('d-m-Y H:i', strtotime($testResult->sample->sampleReception->date_delivered ?? 'N/A')) }}
                    </td>
                    <td class="btop" style="text-align: center"><strong>Result Date:</strong>
                        <br>
                        @if ($testResult->amended_state)
                            {{ date('d-m-Y H:i', strtotime($testResult->amended_at)) }}
                        @else
                            {{ date('d-m-Y H:i', strtotime($testResult->created_at)) }}
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
        {{-- <hr style="height:0.6px; width:100%; color:#6C757D; display:none"> --}}
        @if ($testResult->test->result_type === 'Multiple')
        <tr>
            <table style="text-align: left" class="table dt-responsive nowrap" width="100%" border="1"
                id="parameters">
                <thead>
                    <tr>
                        <th>Test</th>
                        <th>Result</th>
                        <th>Comment</th>
                    </tr>
                </thead>
                <tbody style="text-align: left">
                    @php
                        $test_results = json_decode($testResult->result, true);
                    @endphp
                    @foreach ($test_results as $result)
                        <tr style="text-align: left">
                            <td style="text-align: left">{{ $result['test'] }}</td>
                            <td style="text-align: left">{{ $result['result'] }}</td>
                            <td style="text-align: left">{{ $result['comment'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </tr>
    @else
        <table class="table dt-responsive nowrap" width="100%">
            <tbody>
                {{-- RESULT AND BARCODE --}}
                <tr>
                    @if ($testResult->test->result_presentation == 'Tabular' && $testResult->parameters)
                        <table class="table dt-responsive nowrap" width="100%" border="1" id="parameters">
                            <thead>
                                @if ($testResult->test->parameter_uom)
                                <tr>
                                    <th colspan="{{count($testResult->parameters)+1}}">
                                        {{$testResult->test->parameter_uom}}
                                    </th>

                                </tr>
                                @endif

                                <tr>
                                    @foreach (array_keys($testResult->parameters) as $key)
                                        <th>
                                            {{ $key }}
                                        </th>
                                    @endforeach
                                    <th>
                                        Result
                                    </th>
                                </tr>
                                <tr>
                                    @foreach (array_values($testResult->parameters) as $parameter)
                                        <td>
                                            {{ $parameter }}
                                        </td>
                                    @endforeach
                                    <td>
                                        {{ $testResult->result }}
                                    </td>
                                </tr>
                            </thead>
                        </table>
                    @elseif($testResult->test->result_presentation == 'Non-Tabular' && $testResult->parameters)
                        <td class="btop" style="width:60%; color:#1A2232">
                            <div><b style="font-size: 18px">Results:</b>
                                @if ($testResult->result)
                                    <span>{{ $testResult->result }}</span>
                                @else
                                    <a href="{{ route('attachment.download', $testResult->id) }}">See Attachment</a>
                                @endif
                                <br>
                                @foreach ($testResult->parameters as $key => $parameter)
                                    <i>{{ $key }}</i> :{{ $parameter }}<br>
                                @endforeach
                            </div>
                        </td>
                    @else
                        <td class="btop" style="width:60%; color:#1A2232">
                            <div><b style="font-size: 18px">Results:</b>
                                </em>
                                @if ($testResult->result)
                                    <em>{{ $testResult->result }}</em>
                                @else
                                    <a href="{{ route('attachment.download', $testResult->id) }}">See Attachment</a>
                                @endif
                                </em>
                            </div>
                        </td>
                    @endif
                </tr>
                <br>
            </tbody>
        </table>

        <table class="table dt-responsive nowrap" width="100%">
            <tbody>
                {{-- COMMENT --}}
                <tr style="border-bottom: 0px solid rgb(f, f, f); margin-top: 20px">
                    <td colspan="3" class="btop" style="width:80%">
                        <div
                            style="display:block; border: 1px solid rgb(221, 213, 213); border-radius: 4px; padding-right:10px; padding-left:10px; line-height:1">
                            <div><b style="font-size: 15px">Comments:</b>
                                <p style="font-size: 13px"> <em>{{ $testResult->comment }}</em> <br>
                                </p>
                                <br>
                            </div>
                    </td>
                    <td class="btop" style="width:20%">
                        <div style="float: right;">
                            <img src="data:image/png;base64,
                            @php
                                try {
                                    $qrCode = QrCode::format('svg')->size(84)->generate(
                                        $testResult->tracker .
                                        '|' .
                                        ($testResult->sample->participant ? $testResult->sample->participant->identity : '') .
                                        '|' .
                                        ($testResult->sample ? $testResult->sample->sample_identity : '')
                                    );

                                    echo base64_encode($qrCode);
                                } catch (\Throwable $e) {
                                    $qrCode = QrCode::format('svg')->size(84)->generate($testResult->tracker);
                                    echo base64_encode($qrCode);
                                }
                            @endphp

                            ">
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    @endif
        <br>
        <table class="table dt-responsive nowrap" width="100%" style="text-align: center; ">
            <tbody>
                <tr style="font-size:9px;">
                    <td><b>Kit Used:</b> {{ $testResult->kit->name ?? 'N/A' }}</td>
                    <td style="text-align: center;"><b>Verified Kit Lot:</b> {{ $testResult->verified_lot ?? 'N/A' }}
                    </td>
                    <td style="text-align: right"><b>Kit Expiry Date:</b> {{ $testResult->kit_expiry_date ?? 'N/A' }}
                    </td>
                </tr>
                <br>
                {{-- SIGNATORIES --}}
                <tr>
                    <td class="btop">
                        @if ($testResult->performer->signature ?? null)
                            <img src="{{ asset('storage/' . $testResult->performer->signature ?? '') }}" alt=""
                                height="5%" width="30%"><br>
                        @endif
                        _____________________
                        <br>
                        <strong>Performed By: </strong><br>


                        {{ $testResult->performer ? $testResult->performer->fullName : 'N/A' }}
                    </td>
                    <td class="btop">
                        @if ($testResult->reviewer->signature ?? null)
                            <img src="{{ asset('storage/' . $testResult->reviewer->signature ?? '') }}" alt=""
                                height="5%" width="30%"><br>
                        @endif
                        _____________________
                        <br>
                        <strong>Reviewed By: </strong><br>

                        {{ $testResult->reviewer ? $testResult->reviewer->fullName : 'N/A' }}
                    </td>
                    <td class="btop">
                        @if ($testResult->approver->signature ?? null)
                            <img src="{{ asset('storage/' . $testResult->approver->signature ?? '') }}" alt=""
                                height="5%" width="30%"><br>
                        @endif
                        _____________________
                        <br>
                        <strong>Approved by: </strong> <br>

                        {{ $testResult->approver ? $testResult->approver->fullName : 'N/A' }}
                    </td>
                </tr>
            </tbody>
        </table>
        @include('reports.sample-management.report-footer')
    </div>


    <script type='text/php'>
        if (isset($pdf))
        {
            $pdf->page_text(60, $pdf->get_height() - 50, "{PAGE_NUM} of {PAGE_COUNT}", null, 12, array(0,0,0));
        }
    </script>
