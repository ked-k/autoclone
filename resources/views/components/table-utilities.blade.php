@props(['display'=>''])
<div class="d-flex align-items-center">
    <div>
        <a href="javascript:;" wire:click='export' class="btn {{$display}} btn-secondary me-2"><i class="bi bi-file-earmark-fill"></i> Export</a>
    </div>
    <div>
        <div class="d-flex align-items-center ml-4 me-2">
            <select wire:model="perPage" class="form-select">
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
    </div>
    {{$slot}}
    <div>
        <div class="d-flex align-items-center ml-4">
            <label for="orderAsc" class="text-nowrap mr-2 mb-0">Order</label>
            <select wire:model="orderAsc" class="form-select">
                <option value="1">Asc</option>
                <option value="0">Desc</option>
            </select>
        </div>
    </div>
     <form class="ms-auto position-relative">
       <div class="position-absolute top-50 translate-middle-y search-icon px-3"><i class="bi bi-search"></i></div>
       <input wire:model.debounce.300ms="search" class="form-control ps-5" type="text" placeholder="search">
     </form>
 </div>
 <hr>