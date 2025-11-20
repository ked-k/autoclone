 <div wire:ignore.self class="modal fade" id="amendedResults" data-bs-backdrop="static"
                data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Result Amendments</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"
                                wire:click="close()"></button>
                        </div> <!-- end modal header -->
                        <div class="modal-body">
                            <div class="row row-cols-1 row-cols-xl-2 row-cols-xxl-3">
                                <div class="col-md-12">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="icon-box bg-light-primary border-0">
                                            <i class="bi bi-prescription text-success"></i><i
                                                class='bx bxs-vial text-success'></i>
                                        </div>
                                        <br>
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Amendment Comment</th>
                                                    <th>Date</th>
                                                    <th>Amended By</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach ($amendedResults as $key => $results)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $results->amendment_comment }}</td>
                                                        <td>{{ $results->created_at }}</td>
                                                        <td>{{ $results->amendedBy->fullName }}</td>
                                                        <td><a href="{{ route('print-original-report', $results->id) }}"
                                                                target="_blank"><strong class="text-warning"
                                                                    title="View Report">
                                                                    <i class="bi bi-eye"></i></strong>
                                                            </a></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!--end row-->

                    </div>
                </div> <!-- end modal content-->
            </div> 