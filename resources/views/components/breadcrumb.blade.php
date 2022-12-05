@if (isset($no_bread))
    
@else  
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3"> @yield('pagename')</div>
   
    <div class="ms-auto">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt">Home</i></a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">@yield('linkname')</li>
              </ol>
            </nav>
          </div>
    </div>
  </div>
  @endif