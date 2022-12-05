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
    </style>
</head>

<body style="line-height:1.2; font-family:times;">
    {{-- REPORT HEADER --}}
    <div class="row">
        <img src="{{ asset('autolab-assets/images/headers/header.png') }}" alt="Makerere University Logo" width="100%"
            style="vertical-align:middle;"
            onerror="this.onerror=null;this.src='{{ asset('images/photos/20220130105722.jpg') }}';">
        <h4 style="text-align:center; font-family:times; color:crimson">
            {{ Str::upper(auth()->user()->laboratory->laboratory_name) }}</h4>
        <hr style="height:0.6px; width:100%; color:#6C757D;">
        <h5 style="text-align:center; font-family:times;"><b>
                @if ($testResult->status != 'Approved')
                    <span style="color: crimson">PRELIMINARY</span>
                @endif RESULT REPORT
            </b> </h5>
    </div>
    {{-- PARTICIPANT AND REQUESTER --}}
    <div style="font-size:16px; margin-top:0px;">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td style="width:50%">
                    <div>
                        <b>PARTICIPANT</b> <br>
                        <b>Lab No: </b>
                        <font> {{ $testResult->sample->lab_no }}</font><br>
                        <b>Participant ID: </b>{{ $testResult->sample->participant->identity }}<br> <b>Sample ID:</b>
                        {{ $testResult->sample->sample_identity }}<br>
                        <b>Name:</b> {{ $testResult->sample->participant->surname ?? 'N/A' }}<br>
                        <b>Age:</b> {{ $testResult->sample->participant->age ?? 'N/A' }} <b>Gender:</b>
                        {{ $testResult->sample->participant->gender ?? 'N/A' }}<br>
                        <b>Study Name:</b> {{ $testResult->sample->study->name ?? 'N/A' }}<br>
                    </div>
                </td>
                <td style="width:5%"></td>
                <td style="width:45%">
                    <div>
                        <b>REQUESTER</b> <br>
                        <b>Name:</b> {{ $testResult->sample->requester->name ?? 'N/A' }}<br>
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
                <tr class="btop">
                    <td class="btop"><strong>Test requested:</strong>{{ $testResult->test->name ?? 'N/A' }}</td>
                    <td class="btop" style="text-align: right"><strong>Sample
                            Type:</strong>{{ $testResult->sample->sampleType->type ?? 'N/A' }}</td>
                </tr>
            </tbody>
        </table>
        <table class="table dt-responsive nowrap" width="100%">
            <tbody>
                <tr style="border-bottom: 0.5px solid rgb(f, f, f); margin-top: 10px; margin-bottom: 10px">
                    <td class="btop"><strong>Collection Date:</strong> <br>
                        {{ date('d-m-Y H:i', strtotime($testResult->sample->date_collected ?? 'N/A')) }}</td>
                    <td class="btop" style="text-align: center"><strong>Date received:</strong> <br>
                        {{ date('d-m-Y H:i', strtotime($testResult->sample->sampleReception->date_delivered ?? 'N/A')) }}
                    </td>
                    <td class="btop" style="text-align: right"><strong>Result Date:</strong>
                        <br>{{ $testResult->created_at }}
                    </td>
                </tr>
            </tbody>
        </table>
        <hr style="height:0.6px; width:100%; color:#6C757D; display:none">
        <table class="table dt-responsive nowrap" width="100%">
            <tbody>
                {{-- RESULT AND BARCODE --}}
                <tr>
                    <td class="btop" style="width:100%">
                        <b>Results:</b>
                        @if ($testResult->result)
                            {{ $testResult->result }}
                        @else
                            <a href="{{ route('attachment.download', $testResult->id) }}">See Attachment</a>
                        @endif

                    </td>
                </tr>
                <br>
            </tbody>
        </table>

        <table class="table dt-responsive nowrap" width="100%">
            <tbody>
                {{-- COMMENT --}}
                <tr style="border-bottom: 0.5px solid rgb(f, f, f); margin-top: 20px">
                    <td colspan="3" class="btop" style="width:80%">
                        <div
                            style="display:block; border: 1px solid rgb(221, 213, 213); border-radius: 4px; padding-right:10px; padding-left:10px; line-height:1">
                            <h3><u>Comments:</u></h3>
                            <p> <b>{{ $testResult->comment }}</b> <br>
                            </p>
                            <br>
                        </div>
                    </td>
                    <td class="btop" style="width:20%">
                        <div style="float: right;">
                            <br>
                            <img src="data:image/png;base64, {!! base64_encode(
                                QrCode::format('svg')->size(84)->generate(
                                        $testResult->tracker .
                                            '|' .
                                            $testResult->sample->participant->identity .
                                            '|' .
                                            $testResult->sample->sample_identity,
                                    ),
                            ) !!} ">
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <br>
        <br>
        <table class="table dt-responsive nowrap" width="100%" style="text-align: center">
            <tbody>
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
                    <td>
                        <p style="text-align:center; font-size:10px; color:#4CAF50"> Printed
                            {{ $testResult->download_count }} time(s) @if ($testResult->tracker != '')
                                [{{ $testResult->tracker }}]
                            @endif
                            </font>
                        </p>
                    </td>
                </tr>
            </table>
            {{-- <table style="border-bottom: 0.2px solid #6C757D; width: 100%">
                  <tr>
                    <td  style="color:#6C757D">  Page <span class="page">1</span> of <span class="topage">1</span></td>
                  
                  </tr>
                </table> --}}
        </footer>
    </div>

    <script type='text/php'>
        if (isset($pdf)) 
        {               
            $pdf->page_text(60, $pdf->get_height() - 50, "{PAGE_NUM} of {PAGE_COUNT}", null, 12, array(0,0,0));
        }
    </script>
</body>

</html>
