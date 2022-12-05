<x-app-layout>
    <!-- start page title -->
    @section('title', 'Dashboard')
    @section('pagename', 'Dashboard')
    @section('linkname', 'Dashboard')
    <!-- end page title -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 row-cols-xxl-4">
           
        <div class="col">
          <div class="card radius-10 border-start border-purple border-3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Total Samples</p>
                        <h4 class="my-1">4805</h4>
                        <p class="mb-0 font-13 text-success"><i class="bi bi-caret-up-fill"></i> <br>Registered</p>
                    </div>
                    <div class="widget-icon-large bg-gradient-purple text-white ms-auto"><i class="bi bi-basket2-fill"></i>
                    </div>
                </div>
            </div>
          </div>
         </div>
         <div class="col">
            <div class="card radius-10 border-start border-success border-3">
              <div class="card-body">
                  <div class="d-flex align-items-center">
                      <div>
                          <p class="mb-0 text-secondary">Tested this Week</p>
                          <h4 class="my-1">$24K</h4>
                          <p class="mb-0 font-13 text-success"><i class="bi bi-caret-up-fill"></i> 4.6 from last week</p>
                      </div>
                      <div class="widget-icon-large bg-gradient-success text-white ms-auto"><i class="bi bi-currency-exchange"></i>
                      </div>
                  </div>
              </div>
          </div>
         </div>
         <div class="col">
          <div class="card radius-10 border-start border-danger border-3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Tested this Month</p>
                        <h4 class="my-1">5.8K</h4>
                        <p class="mb-0 font-13 text-danger"><i class="bi bi-caret-down-fill"></i> 2.7 from last month</p>
                    </div>
                    <div class="widget-icon-large bg-gradient-danger text-white ms-auto"><i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
         </div>
         </div>
         <div class="col">
          <div class="card radius-10 border-start border-info border-3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Tested this Year</p>
                        <h4 class="my-1">38.15%</h4>
                        <p class="mb-0 font-13 text-success"><i class="bi bi-caret-up-fill"></i> 12.2% from last year</p>
                    </div>
                    <div class="widget-icon-large bg-gradient-info text-white ms-auto"><i class="bi bi-bar-chart-line-fill"></i>
                    </div>
                </div>
            </div>
          </div>
         </div>
    </div><!--end row-->



</x-app-layout>
