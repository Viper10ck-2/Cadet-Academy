<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-5 py-2.5 bg-gradient-to-r from-gold to-gold-500 hover:from-gold-600 hover:to-gold border border-transparent rounded-xl font-bold text-sm text-navy focus:outline-none focus:ring-2 focus:ring-gold/40 focus:ring-offset-2 dark:focus:ring-offset-navy-800 shadow-lg shadow-gold/20 hover:shadow-xl hover:shadow-gold/30 transition-all duration-200']) }}>
    {{ $slot }}
</button>
