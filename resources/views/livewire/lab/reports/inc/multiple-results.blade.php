<style>
    /* PRINTING STYLES */
    @media print {
        body * {
            visibility: hidden !important;
        }

        .card.report,
        .card.report * {
            visibility: visible !important;
        }

        .card.report {
            position: absolute !important;
            left: 0;
            top: 0;
            width: 100vw !important;
            min-height: 100vh !important;
            background: #fff !important;
            box-shadow: none !important;
            margin: 0 !important;
            padding: 2rem !important;
            z-index: 9999 !important;
        }

        #printBtn {
            display: none !important;
        }

        a[href]:after {
            content: none !important;
        }
    }

    /* GENERAL CARD STYLING */
    .card.report {
        border-radius: 8px;
        box-shadow: 0 2px 20px rgba(0, 0, 0, 0.10);
        border: 1px solid #e0e0e0;
        background: #fafbfc;
        margin: 2rem auto;
        padding: 2rem;
        max-width: 1000px;
        font-family: 'Arial', sans-serif;
        color: #333;
    }

    .card-header {
        text-align: center;
        border-bottom: 1px solid #ccc;
        padding-bottom: 1rem;
    }

    .card-header h1 {
        display: inline-block;
        margin: 0;
        font-size: 24px;
        color: #1976d2;
    }

    .card-header h1,
    .card-header h2,
    .card-header h3 {
        margin: 0.5rem 0;
        font-weight: 600;
        font-size: 16px;
    }

    .card-header em h2 {
        font-size: 18px;
        color: rgb(8, 219, 131);
        font-style: italic;
        margin-top: 0.5rem;
    }

    .card-body {
        margin-top: 2rem;
    }

    .card-body h4 {
        font-size: 20px;
        margin-bottom: 1rem;
        color: #1976d2;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        /* border: 1px solid #e0e0e0; */
        padding: 0.55rem;
        font-size: 0.95rem;
        text-align: left;
        vertical-align: top;
    }

    th {
        background: #1976d2;
        color: rgb(32, 32, 32);
        font-weight: 600;
        font-size: 0.95rem;
    }

    footer {
        margin-top: 2rem;
        font-size: 10px;
        text-align: center;
        color: #555;
    }

    footer img {
        vertical-align: middle;
        margin: 0 5px;
    }

    footer p {
        margin: 2px 0;
    }

    #printBtn {
        position: fixed;
        top: 50%;
        right: 20px;
        transform: translateY(-50%);
        z-index: 10000;
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 12px 18px;
        border-radius: 50px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    #printBtn:hover {
        background-color: #388E3C;
    }

    @media print {
        #printBtn {
            display: none !important;
        }
    }


    @media (max-width: 768px) {
        .card.report {
            padding: 1rem;
        }

        th,
        td {
            font-size: 0.85rem;
        }
    }
</style>



<!-- Print Button -->


<div class="card report">
    <div class="card-header">
        <div style="text-align: center; line-height: 1.7px">
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
        <div style="font-size:16px; margin-top:0px;">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="width:50%">
                        <div>
                            <br>
                            <b>Lab No: </b><em>Refer to the table below</em><br>
                            <b>Participant ID: </b><em>Refer to the table below</em><br>
                            <b>Sample ID:</b><em>Refer to the table below</em><br>
                            <b>Name:</b><em>Refer to the table below</em><br>
                            <b>Age:</b>N/A
                            <b>Address:</b>N/A<br>
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
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <h4>Results</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Lab No</th>
                            <th>Participant</th>
                            <th>Sample_Id</th>
                            <th>Test Name</th>
                            <th>Result</th>
                            <th>Result Date</th>
                            <th>Performed By</th>
                            <th>Reviewed By</th>
                            <th>Approved By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($results as $testResult)
                            <tr>
                                <td>{{ $testResult->sample->lab_no }}</td>
                                <td>{{ $testResult->sample->participant->identity ?? 'N/A' }}</td>
                                <td>{{ $testResult->sample->sample_identity ?? 'N/A' }}</td>
                                <td>{{ $testResult->test->name }}</td>
                                <td>
                                    @if ($testResult->result)
                                        {{ $testResult->result }}<br>
                                        @if ($testResult->comment)
                                            <strong class="text-inverse">Comments:</strong>
                                            <p>{{ $testResult->comment }}</p>
                                        @endif
                                    @else
                                        <a href="{{ route('attachment.download', $testResult->id) }}">See
                                            Attachment</a>
                                    @endif
                                </td>
                                <td>
                                    </strong>{{ date('d-m-Y H:i', strtotime($testResult->created_at)) }}
                                </td>
                                <td>
                                    {{ $testResult->performer ? $testResult->performer->fullName ?? '' : 'N/A' }}
                                </td>
                                <td>
                                    {{ $testResult->performer ? $testResult->reviewer->fullName ?? '' : 'N/A' }}
                                </td>
                                <td>
                                    {{ $testResult->performer ? $testResult->approver->fullName ?? '' : 'N/A' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- @include('reports.sample-management.report-footer') --}}
        </div>
    </div>
    <footer style="width: 100%;  bottom: 0;  line-height: 0.2;">

        <table width="100%" style="margin-top:0.1px; margin-bottom:-8px; padding:1px">
            <tr>
                <td colspan="2" style="font-size:10px; text-align:center">

                    <img width="160px" style="margin-right:1px; " src="{{ asset('autolab-assets/images/sanas.png') }}"
                        alt="SANAS#M0857">
                    {{-- <p style="color:green;  ">
                    This laboratory is accredited by the South African National Accreditation System (SANAS) <br>
                </p> --}}
                    @if ($testResult->test->is_sanas_accredited == 0)
                        <em>
                            <p>These Results are not part of the SANAS Scope of Accreditation for this GMI Labs
                            </p>
                        </em>
                    @endif

                </td>
                <td style="font-size:10px; text-align:center; vertical-align: top;">
                    <div
                        style="
        position: relative;
        display: inline-block;
        width: 180px;
        height: 100px;
    ">
                        <img src="{{ asset('autolab-assets/images/stamp.png') }}" alt="Stamp"
                            style="width: 100%; height: 100%; object-fit: contain; display: block; border: none;" />

                        <div
                            style="
            position: absolute;
            top: 50%;
            left: 56%;
            transform: translateX(-50%);
            color: red;
            font-family: 'Arial Black', sans-serif;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            white-space: nowrap;
        ">
                            {{ date('d M Y') }}
                        </div>
                    </div>
                </td>


            </tr>
            <tr>
                <td colspan="3"style="text-align:center;  font-size:10px; color:#070707">
                    <p>
                        <span style="text-align:left; font-size:10px; color:#4CAF50">Printed By: <font>
                                {{ Auth::user()->name }} </font>
                        </span>
                        <span style="text-align:center; font-size:10px; color:#4CAF50"> Print Date:
                            {{ date('l d-M-Y H:i:s') }}
                        </span>
                        <span style="text-align:right; font-size:10px; color:#4CAF50"> Printed
                            {{ $testResult->download_count }} time(s) @if ($testResult->tracker != '')
                                [{{ $testResult->tracker }}]
                            @endif
                        </span>
                    </p><br>
                    <p style="font-style: italic;">

                        Website: <a style="color:#070707" href="https://gmi.mak.ac.ug">www.gmi.mak.ac.ug</a> |
                        Email: <a style="color:#070707" href="mailto:makbrc.chs@mak.ac.ug">makbrc.chs@mak.ac.ug</a> |
                        Telephone: <a style="color:#070707" href="tel:+256 414674494">+256 414674494</a>
                    </p>
                </td>
            </tr>

        </table>
    </footer>
    <button class="btn btn-success" id="printBtn" onclick="window.print()">Print Results</button>
</div>
