@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'text-center text-success']) }}>
        {{ $status }}
    </div>
@endif
