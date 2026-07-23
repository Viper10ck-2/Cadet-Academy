{{-- Partial: Stats + List --}}
@php
    $tabs = [
        ''       => ['label' => 'Semua',  'count' => $counts['all']],
        'TIU'    => ['label' => 'TIU',    'count' => $counts['TIU']],
        'TWK'    => ['label' => 'TWK',    'count' => $counts['TWK']],
        'TKP'    => ['label' => 'TKP',    'count' => $counts['TKP']],
        'TBI'    => ['label' => 'TBI',    'count' => $counts['TBI']],
        'uncategorized' => ['label' => 'Lainnya', 'count' => $counts['uncategorized']],
    ];
    $activeCategory = request('category', '');
@endphp

{{-- Filter Tabs (inline pills) --}}
<div class="flex items-center gap-1.5 mb-3 flex-wrap">
    @foreach($tabs as $key => $tab)
    <a href="{{ route('admin.questions.index', $key ? ['category' => $key] : []) }}"
       class="stats-filter inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium transition-all
              {{ ($activeCategory === $key) ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}">
        {{ $tab['label'] }}
        <span class="{{ ($activeCategory === $key) ? 'text-indigo-200' : 'text-gray-400' }}">{{ $tab['count'] }}</span>
    </a>
    @endforeach
</div>

{{-- Questions List --}}
@if($questions->isEmpty())
<div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700 p-8 text-center">
    <p class="text-gray-400 text-sm">Belum ada soal. Klik <strong>Tambah Soal</strong> untuk memulai.</p>
</div>
@else
<div class="space-y-1.5">
    @foreach($questions as $q)
    <div id="question-{{ $q->id }}" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700 px-3.5 py-2.5 hover:border-gray-300 dark:hover:border-gray-600 transition-all group">
        <div class="flex items-start gap-3">
            <span class="q-number text-[11px] text-gray-400 font-mono pt-0.5 shrink-0 w-5 text-right">{{ $loop->index + 1 + (($questions->currentPage() - 1) * $questions->perPage()) }}</span>

            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-1.5 mb-0.5">
                    @if($q->category)
                    <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded
                        {{ $q->category === 'TIU' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400' : '' }}
                        {{ $q->category === 'TWK' ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400' : '' }}
                        {{ $q->category === 'TKP' ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400' : '' }}
                        {{ $q->category === 'TBI' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400' : '' }}">{{ $q->category }}</span>
                    @endif
                    <span class="text-[10px] text-gray-400">{{ $q->type === 'multiple_choice' ? 'PG' : 'Essay' }}</span>
                </div>
                <p class="text-sm text-gray-900 dark:text-white">{{ $q->question_text }}</p>
                @if($q->type === 'multiple_choice' && $q->options)
                <div class="mt-1 flex flex-wrap gap-x-4 gap-y-0 text-xs text-gray-500 dark:text-gray-400">
                    @foreach($q->options as $key => $val)
                        <span class="{{ $key === $q->correct_answer ? 'text-green-600 dark:text-green-400 font-semibold' : '' }}">{{ $key }}. {{ $val }}</span>
                    @endforeach
                </div>
                @endif
            </div>

            <div class="flex items-center gap-0.5 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                <button onclick="editQuestion({{ $q->id }})" title="Edit"
                        class="w-6 h-6 rounded flex items-center justify-center text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
                <button onclick="deleteQuestion({{ $q->id }})" title="Hapus"
                        class="w-6 h-6 rounded flex items-center justify-center text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="mt-3">
    {{ $questions->links() }}
</div>
@endif
