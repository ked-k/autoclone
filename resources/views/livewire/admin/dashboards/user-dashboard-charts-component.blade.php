<div>
    <div class="row g-3">
        <div class="col-12 col-lg-12 col-xl-12 d-flex">
            <div class="card radius-10 w-100">
                <div class="card-body">
                    <div class="row row-cols-1 row-cols-lg-2 g-3 align-items-center">
                        <div class="col">
                            <h5 class="mb-0">Monthly Samples</h5>
                        </div>
                        <div class="col">
                            <div class="d-flex align-items-center justify-content-sm-end gap-3 cursor-pointer">
                                <div class="font-13"><i class="bi bi-circle-fill text-primary"></i><span
                                        class="ms-2">{{ date('Y') }}</span></div>
                                <div class="font-13"><i class="bi bi-circle-fill text-success"></i><span
                                        class="ms-2">{{ date('Y') - 1 }}</span></div>
                            </div>
                        </div>
                    </div>
                    <div id="samplesChart"></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-12 col-xl-12 d-flex">
            <div class="card radius-10 w-100">
                <div class="card-body">
                    <div class="row row-cols-1 row-cols-lg-2 g-3 align-items-center">
                        <div class="col">
                            <h5 class="mb-0">Monthly Tests</h5>
                        </div>
                        <div class="col">
                            <div class="d-flex align-items-center justify-content-sm-end gap-3 cursor-pointer">
                                <div class="font-13"><i class="bi bi-circle-fill text-primary"></i><span
                                        class="ms-2">{{ date('Y') }}</span></div>
                                <div class="font-13"><i class="bi bi-circle-fill text-success"></i><span
                                        class="ms-2">{{ date('Y') - 1 }}</span></div>
                            </div>
                        </div>
                    </div>
                    <div id="testsChart"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('autolab-assets/plugins/apexcharts-bundle/js/apexcharts.min.js') }}"></script>
        <script>
            // Monthly samples line chart
            var options = {
                series: [{
                        name: {{ date('Y') }},
                        data: [
                            @foreach ($labels['month'] as $month)
                                {{ $currentYearSampleData->where('month_name', $month)->first()->count ?? 0 }},
                            @endforeach
                        ]
                    },
                    {
                        name: {{ date('Y') - 1 }},
                        data: [
                            @foreach ($labels['month'] as $month)
                                {{ $previousYearSampleData->where('month_name', $month)->first()->count ?? 0 }},
                            @endforeach
                        ]
                    }
                ],
                chart: {
                    foreColor: '#9a9797',
                    type: "area",
                    //width: 130,
                    height: 360,
                    toolbar: {
                        show: !1
                    },
                    zoom: {
                        enabled: !1
                    },
                    dropShadow: {
                        enabled: 0,
                        top: 3,
                        left: 14,
                        blur: 4,
                        opacity: .12,
                        color: "#3461ff"
                    },
                    sparkline: {
                        enabled: !1
                    }
                },
                markers: {
                    size: 0,
                    colors: ["#3461ff", "#12bf24"],
                    strokeColors: "#fff",
                    strokeWidth: 2,
                    hover: {
                        size: 7
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: !1,
                        columnWidth: "35%",
                        endingShape: "rounded"
                    }
                },
                legend: {
                    show: false,
                    position: 'top',
                    horizontalAlign: 'left',
                    offsetX: -20
                },
                dataLabels: {
                    enabled: !1
                },
                grid: {
                    show: true,
                    // borderColor: '#eee',
                    // strokeDashArray: 4,
                },
                stroke: {
                    show: !0,
                    width: 3,
                    curve: "smooth"
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: "vertical",
                        shadeIntensity: 0.5,
                        gradientToColors: ["#3461ff", "#12bf24"],
                        inverseColors: true,
                        opacityFrom: 0.8,
                        opacityTo: 0.2,
                        //stops: [0, 50, 100],
                        //colorStops: []
                    }
                },
                colors: ["#3461ff", "#12bf24"],
                xaxis: {
                    categories: [
                        @foreach ($labels['month'] as $month)
                            "{{ $month }}",
                        @endforeach
                    ]

                },
                tooltip: {
                    theme: 'dark',
                    y: {
                        formatter: function(val) {
                            return "" + val + ""
                        }
                    }
                }
            };

            var samples_chart = new ApexCharts(document.querySelector("#samplesChart"), options);
            samples_chart.render();


            // Monthly samples line chart
            var options2 = {
                series: [{
                        name: {{ date('Y') }},
                        data: [
                            @foreach ($monthTestLabels['month'] as $month)
                                {{ $currentYearTestData->where('month_name', $month)->first()->count ?? 0 }},
                            @endforeach
                        ]
                    },
                    {
                        name: {{ date('Y') - 1 }},
                        data: [
                            @foreach ($monthTestLabels['month'] as $month)
                                {{ $previousYearTestData->where('month_name', $month)->first()->count ?? 0 }},
                            @endforeach
                        ]
                    }
                ],
                chart: {
                    foreColor: '#9a9797',
                    type: "area",
                    //width: 130,
                    height: 360,
                    toolbar: {
                        show: !1
                    },
                    zoom: {
                        enabled: !1
                    },
                    dropShadow: {
                        enabled: 0,
                        top: 3,
                        left: 14,
                        blur: 4,
                        opacity: .12,
                        color: "#3461ff"
                    },
                    sparkline: {
                        enabled: !1
                    }
                },
                markers: {
                    size: 0,
                    colors: ["#3461ff", "#12bf24"],
                    strokeColors: "#fff",
                    strokeWidth: 2,
                    hover: {
                        size: 7
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: !1,
                        columnWidth: "35%",
                        endingShape: "rounded"
                    }
                },
                legend: {
                    show: false,
                    position: 'top',
                    horizontalAlign: 'left',
                    offsetX: -20
                },
                dataLabels: {
                    enabled: !1
                },
                grid: {
                    show: true,
                    // borderColor: '#eee',
                    // strokeDashArray: 4,
                },
                stroke: {
                    show: !0,
                    width: 3,
                    curve: "smooth"
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: "vertical",
                        shadeIntensity: 0.5,
                        gradientToColors: ["#3461ff", "#12bf24"],
                        inverseColors: true,
                        opacityFrom: 0.8,
                        opacityTo: 0.2,
                        //stops: [0, 50, 100],
                        //colorStops: []
                    }
                },
                colors: ["#3461ff", "#12bf24"],
                xaxis: {
                    categories: [
                        @foreach ($monthTestLabels['month'] as $month)
                            "{{ $month }}",
                        @endforeach
                    ]

                },
                tooltip: {
                    theme: 'dark',
                    y: {
                        formatter: function(val) {
                            return "" + val + ""
                        }
                    }
                }
            };

            var tests_chart = new ApexCharts(document.querySelector("#testsChart"), options2);
            tests_chart.render();
        </script>
    @endpush
</div>
