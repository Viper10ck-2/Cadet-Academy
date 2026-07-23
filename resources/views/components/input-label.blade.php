@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-sm text-navy dark:text-gray-200 mb-1.5']) }}>
    {{ $value ?? $slot }}
</label>
