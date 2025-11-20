<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Combined Test Result Report</title>
    <style>
        body {
            margin: auto;           
            font-family: Nunito, sans-serif;
            font-size: .8rem;
            font-weight: 400;
            line-height: 1.5;
            color: #000000;
            background-color: #ffffff;
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: transparent
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
        .report-wrapper{
            width: 70%;
            height: auto;
            margin: auto;
            border: 1px solid #cbcbcb;
            background: white;            
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

        @media print{
            .report-wrapper{
            width: 100%;
            height: auto;
            margin: 2px auto;
            border: 0px;
            background: white;            
        }
        }
    </style>
    <script>
        window.print()
    </script>
</head>

<body style="line-height:1.2; font-family:times;">
    {{-- REPORT HEADER --}}
    <div class="report-wrapper">
        <div class="row">
            {{-- <img src="{{ asset('autolab-assets/images/headers/header-min.png') }}" alt="Makerere University Logo" width="100%"
                style="vertical-align:middle;"
                onerror="this.onerror=null;this.src='{{ asset('images/photos/20220130105722.jpg') }}';"> --}}
            <div style="text-align: center; line-height: 2px">
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
                <h2><b>COLLEGE OF HEALTH SCIENCES</b></h2>
                <h3><b>School of Biomedical Sciences</b></h3>
                <h3><b>Department of Immunology and Molecular Biology</b></h3>
                <em>
                    <h2 style="color:rgb(8, 219, 131)"> Genomics, Molecular and Immunology Laboratories </h2>
                </em>
            </div>


            <hr style="height:0.6px; width:100%; color:#6C757D;">
            <h3 style="text-align:center; font-size:20px;color: crimson; margin-top:-15px"><b>
                    Result Report
                </b> </h3>
        </div>
        {{-- PARTICIPANT AND REQUESTER --}}
        <div style="font-size:16px; margin-top:0px;">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="width:50%">
                        <div>
                            <b>Study Name:</b> {{ $testResults[0]->sample->study->name ?? 'N/A' }}<br>
                            <b>Sample Type:</b> {{ $testResults[0]->sample->sampleType->type ?? 'N/A' }}<br>
                            <b>Test Requested:</b> {{ $testResults[0]->test->name ?? 'N/A' }}<br>
                        </div>
                    </td>
                    <td style="width:5%"></td>
                    <td style="width:45%">
                        <div>
                            <b>Requester</b> <br>
                            {{ $testResults[0]->sample->requester->name ?? 'N/A' }}<br>

                            {{ $testResults[0]->sample->participant->facility->name ?? 'N/A' }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-12 table-responsive" style="font-size:15px; margin-top:20px; text-align: center">

            <hr style="height:0.6px; width:100%; color:#6C757D; display:none">
            {{-- RESULT AND BARCODE --}}
            @if ($testResults[0]->test->result_presentation == 'Tabular' && $testResults[0]->parameters)
                <table class="table dt-responsive nowrap" width="100%" border="1" id="parameters">
                    <thead>
                        @if ($testResults[0]->test->parameter_uom)
                            <tr>
                                <th colspan="{{ count($testResults[0]->parameters) + 4 }}">
                                    Results ({{ $testResults[0]->test->parameter_uom }})
                                </th>
                            </tr>
                        @endif

                        <tr>
                            <th>
                                Participant ID
                            </th>
                            <th>
                                Sample ID
                            </th>
                            <th>
                                Date Received
                            </th>
                            @foreach (array_keys($testResults[0]->parameters) as $key)
                                <th>
                                    {{ $key }}
                                </th>
                            @endforeach
                            <th>
                                Result
                            </th>
                        </tr>
                    </thead>

                    @foreach ($testResults as $testResult)
                        <tr>
                            <td>{{ $testResult->sample->participant->identity }}</td>
                            <td>{{ $testResult->sample->sample_identity }}</td>
                            <td>{{ date('d-m-Y', strtotime($testResult->sample->sampleReception->date_delivered)) }}</td>
                            @foreach (array_values($testResult->parameters) as $parameter)
                                <td>
                                    {{ $parameter }}
                                </td>
                            @endforeach
                            <td>
                                {{ $testResult->result }}
                            </td>
                        </tr>
                    @endforeach
                </table>
            @elseif($testResults[0]->test->result_presentation == 'Non-Tabular' && $testResults[0]->parameters)
                <table class="table dt-responsive nowrap" width="100%" border="1">
                    <thead>
                        <tr>
                            <th>
                                Participant ID
                            </th>
                            <th>
                                Sample ID
                            </th>
                            <th>
                                Result
                            </th>
                            <th>
                                Result Date
                            </th>
                        </tr>
                    </thead>
                    @foreach ($testResults as $testResult)
                        <tr>
                            <td>{{ $testResult->sample->participant->identity }}</td>
                            <td>{{ $testResult->sample->sample_identity }}</td>
                            <td style="white-space: normal">
                                @if ($testResult->result)
                                    <strong>{{ $testResult->result }}</strong>
                                @else
                                    <a href="{{ route('attachment.download', $testResult->id) }}">See Attachment</a>
                                @endif
                                <br>
                                @foreach ($testResult->parameters as $key => $parameter)
                                    <small>{{ $key }}</small> :{{ $parameter }}<br>
                                @endforeach
                            </td>
                            <td>
                                {{ $testResult->created_at }}
                            </td>
                        </tr>
                    @endforeach

                </table>
            @else
                <table class="table dt-responsive nowrap" width="100%" border="1">
                    <thead>
                        <tr>
                            <th>
                                Participant ID
                            </th>
                            <th>
                                Sample ID
                            </th>
                            <th>
                                Result
                            </th>
                            <th>
                                Result Date
                            </th>
                        </tr>
                    </thead>
                    @foreach ($testResults as $testResult)
                        <tr>
                            <td>{{ $testResult->sample->participant->identity }}</td>
                            <td>{{ $testResult->sample->sample_identity }}</td>
                            <td>
                                @if ($testResult->result)
                                    <strong>{{ $testResult->result }}</strong>
                                @else
                                    <a href="{{ route('attachment.download', $testResult->id) }}">See Attachment</a>
                                @endif
                            </td>
                            <td>
                                {{ $testResult->created_at }}
                            </td>
                        </tr>
                    @endforeach

                </table>
            @endif

            <table class="table dt-responsive nowrap" width="100%">
                <tbody>
                    {{-- COMMENT --}}
                    <tr style="border-bottom: 0.5px solid rgb(f, f, f); margin-top: 10px">

                        <td colspan="3" style="width:80% ; text-align: left;">
                            <b>Comments</b><br>
                            @foreach ($testResults as $testResult)
                                <b>{{ $testResult->sample->sample_identity }}</b>: {{ $testResult->comment }}<br>
                            @endforeach
                        </td>

                        <td style="width:20%">
                            <div style="float: right;">
                                <br>
                                {{ QrCode::size(84)->generate($qrCodeContent) }}
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <br>
            <br>
            <table class="table dt-responsive nowrap" width="100%" style="text-align: center; ">
                <tbody>
                    <tr style="font-size:9px;">
                        <td><b>Kit Used:</b> {{ $testResults[0]->kit->name ?? 'N/A' }}</td>
                        <td style="text-align: center;"><b>Verified Kit Lot:</b>
                            {{ $testResults[0]->verified_lot ?? 'N/A' }}
                        </td>
                        <td style="text-align: right"><b>Kit Expiry Date:</b>
                            {{ $testResults[0]->kit_expiry_date ?? 'N/A' }}
                        </td>
                    </tr>
                    <br>
                    {{-- SIGNATORIES --}}
                    <tr>
                        <td class="btop">
                            @if ($testResults[0]->performer->signature ?? null)
                                <img src="{{ asset('storage/' . $testResults[0]->performer->signature ?? '') }}"
                                    alt="" height="5%" width="30%"><br>
                            @endif
                            _____________________
                            <br>
                            <strong>Performed By: </strong><br>
                            {{ $testResults[0]->performer ? $testResults[0]->performer->fullName : 'N/A' }}
                        </td>
                        <td class="btop">
                            @if ($testResults[0]->reviewer->signature ?? null)
                                <img src="{{ asset('storage/' . $testResults[0]->reviewer->signature ?? '') }}"
                                    alt="" height="5%" width="30%"><br>
                            @endif
                            _____________________
                            <br>
                            <strong>Reviewed By: </strong><br>

                            {{ $testResults[0]->reviewer ? $testResults[0]->reviewer->fullName : 'N/A' }}
                        </td>
                        <td class="btop">
                            @if ($testResults[0]->approver->signature ?? null)
                                <img src="{{ asset('storage/' . $testResults[0]->approver->signature ?? '') }}"
                                    alt="" height="5%" width="30%"><br>
                            @endif
                            _____________________
                            <br>
                            <strong>Approved by: </strong> <br>

                            {{ $testResults[0]->approver ? $testResults[0]->approver->fullName : 'N/A' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <footer>
                <table width="100%" style=" position: fixed; bottom: 0;">

                    <tr>
                        <td>
                            <p style="text-align:center; font-size:10px; color:#4CAF50">Printed By: <font>
                                    {{ Auth::user()->name }} </font>
                            </p>
                        </td>
                        <td>
                            <p style="text-align:center; font-size:10px; color:#4CAF50"> Print Date:
                                {{ date('l d-M-Y H:i:s') }}</font>
                            </p>
                        </td>
                        <td style="color:#6C757D"> Page <span class="page">1</span> of <span class="topage">1</span></td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <p style="text-align:center; font-style: italic; font-size:10px; color:#4CAF50">
                      
                                <strong>Contact us on</strong> Tel: <a href="tel:+256 0414674494">0414674494</a> |
                         
                                Website: <a style="color: #44a847" href="https://gmi.mak.ac.ug">www.gmi.mak.ac.ug</a> |
                          
                                Email: <a href="mailto:makbrc.chs@mak.ac.ug">makbrc.chs@mak.ac.ug</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </footer>
        </div>
    </div>


    <script type='text/php'>
        if (isset($pdf))
        {
            $pdf->page_text(60, $pdf->get_height() - 50, "{PAGE_NUM} of {PAGE_COUNT}", null, 12, array(0,0,0));
        }
    </script>
