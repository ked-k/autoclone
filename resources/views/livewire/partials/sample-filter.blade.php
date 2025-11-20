 <div class="row">
     <div class="mb-3 col-md-3">
         <label for="facility_id" class="form-label">Facility</label>
         <select class="form-select select2" id="facility_id" data-model="facility_id" wire:model="facility_id">
             <option selected value="0">All</option>
             @forelse ($facilities as $facility)
                 <option value='{{ $facility->id }}'>{{ $facility->name }}</option>
             @empty
             @endforelse
         </select>
     </div>
     <div class="mb-3 col-md-3">
         <label for="study" class="form-label">Study</label>
         <select class="form-select select2" id="study_id" data-model="study_id" wire:model="study_id">
             <option selected value="0">All</option>
             @forelse ($studies as $study)
                 <option value='{{ $study->id }}'>{{ $study->name }}</option>
             @empty
             @endforelse
         </select>
     </div>
     <div class="mb-3 col-md-2">
         <label for="job" class="form-label">Sample State</label>
         <select class="form-select" id="job" wire:model="job">
             <option selected value="">All</option>
             @forelse ($jobs as $job)
                 <option value='{{ $job->sample_is_for }}'>{{ $job->sample_is_for }}
                 </option>
             @empty
             @endforelse
         </select>
     </div>

     <div class="mb-3 col-md-2">
         <label for="sampleType" class="form-label">Sample Type</label>
         <select class="form-select select2" id="sampleType" data-model="sampleType" wire:model='sampleType'>
             <option selected value="0">All</option>
             @foreach ($sampleTypes as $sampleType)
                 <option value='{{ $sampleType->id }}'>
                     {{ $sampleType->type }}</option>
             @endforeach
         </select>
     </div>
     <div class="mb-3 col-md-2">
         <label for="created_by" class="form-label">Accessioned By</label>
         <select class="form-select" id="created_by" wire:model='created_by'>
             @if (Auth::user()->hasPermission('manager-access|master-access'))
                 <option selected value="0">All</option>
                 @foreach ($users as $user)
                     <option value='{{ $user->id }}'>
                         {{ $user->fullName }}</option>
                 @endforeach
             @else
                 <option selected value="{{ auth()->user()->id }}">
                     {{ auth()->user()->fullName }}</option>
             @endif
         </select>
     </div>
     <div class="mb-3 col-md-2">
         <label for="from_date" class="form-label">Start Date</label>
         <input id="from_date" type="date" class="form-control" wire:model="from_date">
     </div>
     <div class="mb-3 col-md-2">
         <label for="to_date" class="form-label">End Date</label>
         <input id="to_date" type="date" class="form-control" wire:model="to_date">
     </div>
     <div class="mb-2 col-md-2">
         <label for="perPage" class="form-label">Per Page</label>
         <select wire:model="perPage" class="form-select" id="perPage">
             <option value="10">10</option>
             <option value="20">20</option>
             <option value="30">30</option>
             <option value="50">50</option>
             <option value="100">100</option>
             <option value="200">200</option>
             <option value="500">500</option>
             <option value="1000">1000</option>
         </select>
     </div>

     <div class="mb-3 col-md-2">
         <label for="orderBy" class="form-label">OrderBy</label>
         <select wire:model="orderBy" class="form-select">
             <option value="sample_identity">Sample ID</option>
             <option value="lab_no">Lab No</option>
             <option value="id">Latest</option>
         </select>
     </div>

     <div class="mb-3 col-md-2">
         <label for="orderAsc" class="form-label">Order</label>
         <select wire:model="orderAsc" class="form-select" id="orderAsc">
             <option value="1">Asc</option>
             <option value="0">Desc</option>
         </select>
     </div>
     <div class=" col-md-2 ms-auto position-relative">
         <label for="search" class="form-label">Search</label>
         <input wire:model.debounce.300ms="search" class="form-control " type="text" placeholder="search">
     </div>
 </div>
 @push('scripts')
     @include('livewire.layout.select-2')
 @endpush
