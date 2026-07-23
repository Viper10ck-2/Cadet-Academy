@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full border-gray-200 dark:border-navy-600 dark:bg-navy-900/50 dark:text-gray-200 focus:border-gold dark:focus:border-gold-500 focus:ring-gold/30 dark:focus:ring-gold-500/20 rounded-xl px-4 py-2.5 text-sm shadow-sm transition-all duration-200 placeholder:text-gray-400 dark:placeholder:text-gray-500']) }}>
