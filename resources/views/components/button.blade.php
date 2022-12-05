<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-success radius-30']) }}>
    {{ $slot }}
</button>
