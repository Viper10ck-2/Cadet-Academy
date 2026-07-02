<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $exam->title }}</h2>
            <div x-data="examTimer({{ $session->remaining_seconds }})" class="flex items-center gap-2">
                <span class="text-sm text-gray-500">⏱️</span>
                <span x-text="display" class="text-lg font-bold" :class="urgent ? 'text-red-600' : 'text-gray-800 dark:text-white'"></span>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="examApp({{ $session->id }}, {{ $session->total_questions }})">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Progress -->
            <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                    <span>Progress</span>
                    <span x-text="answered + '/' + total"></span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-indigo-600 h-2 rounded-full transition-all" :style="'width: ' + (answered/total*100) + '%'"></div>
                </div>
            </div>

            <!-- Questions -->
            <div class="space-y-6">
                @foreach($questions as $index => $question)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700"
                     id="q-{{ $question->id }}">
                    <div class="flex items-start gap-3">
                        <span class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 flex items-center justify-center font-medium text-sm">{{ $index + 1 }}</span>
                        <div class="flex-1">
                            <p class="text-gray-900 dark:text-white font-medium mb-3">{{ $question->question_text }}</p>

                            @if($question->type === 'multiple_choice' && $question->options)
                            <div class="space-y-2">
                                @foreach($question->options as $key => $option)
                                <label class="flex items-center gap-3 p-3 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-750 transition"
                                       :class="selectedAnswer({{ $question->id }}) === '{{ $key }}' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : ''">
                                    <input type="radio" name="q_{{ $question->id }}" value="{{ $key }}"
                                           @change="saveAnswer({{ $question->id }}, '{{ $key }}')"
                                           {{ isset($answers[$question->id]) && $answers[$question->id] === $key ? 'checked' : '' }}
                                           class="text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $key }}. {{ $option }}</span>
                                </label>
                                @endforeach
                            </div>
                            @else
                            <textarea name="q_{{ $question->id }}" rows="3"
                                      @change="saveAnswer({{ $question->id }}, $event.target.value)"
                                      placeholder="Tulis jawaban Anda..."
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">{{ $answers[$question->id] ?? '' }}</textarea>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Submit -->
            <div class="mt-8 flex justify-end">
                <form action="{{ route('cbt.finish', $session) }}" method="POST" onsubmit="return confirm('Yakin selesaikan ujian? Jawaban yang sudah tersimpan tidak akan hilang.')">
                    @csrf
                    <button class="px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition shadow-lg shadow-green-500/25">
                        ✅ Selesai & Kumpulkan
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('examTimer', (initialSeconds) => ({
                seconds: initialSeconds,
                get display() {
                    const m = Math.floor(this.seconds / 60);
                    const s = this.seconds % 60;
                    return `${m}:${s.toString().padStart(2, '0')}`;
                },
                get urgent() { return this.seconds < 300; },
                init() {
                    this.tick();
                },
                tick() {
                    if (this.seconds <= 0) {
                        alert('Waktu habis! Ujian akan dikumpulkan otomatis.');
                        document.querySelector('form[action*="finish"]').submit();
                        return;
                    }
                    this.seconds--;
                    setTimeout(() => this.tick(), 1000);
                }
            }));

            Alpine.data('examApp', (sessionId, total) => ({
                total: total,
                answered: {{ $session->answered_questions }},
                answers: {},
                selectedAnswer(qid) {
                    return this.answers[qid] || null;
                },
                async saveAnswer(qid, answer) {
                    this.answers[qid] = answer;
                    try {
                        const res = await fetch('{{ route("cbt.answer", $session) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ question_id: qid, answer: answer })
                        });
                        const data = await res.json();
                        if (data.answered !== undefined) this.answered = data.answered;
                        if (data.error) alert(data.error);
                    } catch(e) { console.error(e); }
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>
