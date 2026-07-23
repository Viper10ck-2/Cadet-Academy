<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $exam->title }}</h2>
            <div x-data="timer({{ $session->remaining_seconds }})" class="flex items-center gap-3">
                <span class="text-sm text-gray-500">⏱️ Sisa Waktu</span>
                <span x-text="display" class="text-xl font-bold font-mono" :class="urgent ? 'text-red-600 animate-pulse' : 'text-gray-800 dark:text-white'"></span>
            </div>
        </div>
    </x-slot>

    <div class="py-6" x-data="examApp({{ $session->id }}, {{ Js::from($questions->pluck('id')->toArray()) }}, {{ Js::from($questions->pluck('category')->toArray()) }})">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                {{-- LEFT: Navigasi Soal --}}
                <div class="lg:col-span-1 order-2 lg:order-1">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 sticky top-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Navigasi Soal</h3>

                        {{-- TWK --}}
                        <div class="mb-3">
                            <p class="text-xs font-semibold text-red-600 dark:text-red-400 mb-1.5">TWK (1–30)</p>
                            <div class="grid grid-cols-5 gap-1">
                                <template x-for="i in 30" :key="'twk-'+i">
                                    <button @click="goTo(i - 1)"
                                            :class="[
                                                'w-full aspect-square rounded text-[10px] font-bold transition-all',
                                                isAnswered(i - 1) ? 'bg-green-500 text-white' : 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400',
                                                currentIndex === (i - 1) ? 'ring-2 ring-indigo-500 ring-offset-1 dark:ring-offset-gray-800 scale-110' : ''
                                            ]"
                                            x-text="i"></button>
                                </template>
                            </div>
                        </div>

                        {{-- TIU --}}
                        <div class="mb-3">
                            <p class="text-xs font-semibold text-blue-600 dark:text-blue-400 mb-1.5">TIU (31–65)</p>
                            <div class="grid grid-cols-5 gap-1">
                                <template x-for="i in 35" :key="'tiu-'+i">
                                    <button @click="goTo(30 + i - 1)"
                                            :class="[
                                                'w-full aspect-square rounded text-[10px] font-bold transition-all',
                                                isAnswered(30 + i - 1) ? 'bg-green-500 text-white' : 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400',
                                                currentIndex === (30 + i - 1) ? 'ring-2 ring-indigo-500 ring-offset-1 dark:ring-offset-gray-800 scale-110' : ''
                                            ]"
                                            x-text="30 + i"></button>
                                </template>
                            </div>
                        </div>

                        {{-- TKP --}}
                        <div class="mb-3">
                            <p class="text-xs font-semibold text-green-600 dark:text-green-400 mb-1.5">TKP (66–110)</p>
                            <div class="grid grid-cols-5 gap-1">
                                <template x-for="i in 45" :key="'tkp-'+i">
                                    <button @click="goTo(65 + i - 1)"
                                            :class="[
                                                'w-full aspect-square rounded text-[10px] font-bold transition-all',
                                                isAnswered(65 + i - 1) ? 'bg-green-500 text-white' : 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400',
                                                currentIndex === (65 + i - 1) ? 'ring-2 ring-indigo-500 ring-offset-1 dark:ring-offset-gray-800 scale-110' : ''
                                            ]"
                                            x-text="65 + i"></button>
                                </template>
                            </div>
                        </div>

                        {{-- Legend --}}
                        <div class="flex items-center gap-4 pt-3 border-t border-gray-100 dark:border-gray-700 mt-3">
                            <div class="flex items-center gap-1.5 text-[10px] text-gray-500">
                                <span class="w-3 h-3 rounded bg-green-500"></span> Sudah
                            </div>
                            <div class="flex items-center gap-1.5 text-[10px] text-gray-500">
                                <span class="w-3 h-3 rounded bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700"></span> Belum
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Soal --}}
                <div class="lg:col-span-3 order-1 lg:order-2 space-y-4">
                    {{-- Progress --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-1.5">
                            <span>Progress</span>
                            <span x-text="answeredCount + '/' + totalQuestions"></span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" :style="'width: ' + (answeredCount/totalQuestions*100) + '%'"></div>
                        </div>
                    </div>

                    {{-- Category + Info --}}
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                              :class="{
                                  'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': currentCategory === 'TWK',
                                  'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400': currentCategory === 'TIU',
                                  'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': currentCategory === 'TKP',
                              }" x-text="currentCategory"></span>
                        <span class="text-xs text-gray-400" x-text="'Soal ke-' + (currentIndex + 1) + ' dari 110'"></span>
                    </div>

                    {{-- Question Card --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 min-h-[280px]">
                        <div class="flex items-start gap-4">
                            <span class="flex-shrink-0 w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 flex items-center justify-center font-bold text-sm"
                                  x-text="currentIndex + 1"></span>
                            <div class="flex-1">
                                <p class="text-gray-900 dark:text-white font-medium text-lg leading-relaxed mb-6" x-text="currentQuestion?.question_text || ''"></p>

                                <template x-if="currentQuestion?.type === 'multiple_choice'">
                                    <div class="space-y-3">
                                        <template x-for="(val, key) in currentQuestion?.options || {}" :key="key">
                                            <label class="flex items-center gap-4 p-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-750 transition"
                                                   :class="currentAnswer === key ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : ''">
                                                <input type="radio" :name="'q_'+currentQuestion.id" :value="key"
                                                       :checked="currentAnswer === key"
                                                       @change="saveCurrent(key)"
                                                       class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 shrink-0">
                                                <span class="text-base text-gray-700 dark:text-gray-300" x-text="key + '. ' + val"></span>
                                            </label>
                                        </template>
                                    </div>
                                </template>

                                <template x-if="currentQuestion?.type === 'essay'">
                                    <textarea rows="4" :value="currentAnswer || ''"
                                              @change="saveCurrent($event.target.value)"
                                              placeholder="Tulis jawaban Anda..."
                                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500"></textarea>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Navigation Buttons --}}
                    <div class="flex items-center justify-between">
                        <button @click="prevQuestion"
                                :disabled="currentIndex === 0"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 disabled:opacity-40 disabled:cursor-not-allowed transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Sebelumnya
                        </button>

                        <span class="text-xs text-gray-400" x-text="(currentIndex + 1) + ' / 110'"></span>

                        <button @click="nextQuestion"
                                :disabled="currentIndex >= totalQuestions - 1"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 disabled:opacity-40 disabled:cursor-not-allowed transition shadow-sm shadow-indigo-500/20">
                            Selanjutnya
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>

                    {{-- Submit --}}
                    <div class="flex justify-end pt-2">
                        <form action="{{ route('cbt.finish', $session) }}" method="POST" onsubmit="return confirm('Yakin selesaikan ujian?\n\nJawaban yang sudah diisi akan tetap tersimpan. Soal yang belum dijawab dianggap kosong.')">
                            @csrf
                            <button class="px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition shadow-lg shadow-green-500/25">
                                ✅ Selesai & Kumpulkan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            // ─── Timer ───
            Alpine.data('timer', (initialSeconds) => ({
                seconds: initialSeconds,
                get display() {
                    const m = Math.floor(this.seconds / 60);
                    const s = this.seconds % 60;
                    return `${m}:${s.toString().padStart(2, '0')}`;
                },
                get urgent() { return this.seconds < 300; },
                init() {
                    if (this.seconds > 0) this.tick();
                },
                tick() {
                    if (this.seconds <= 0) {
                        alert('⏰ Waktu habis! Ujian akan dikumpulkan otomatis.');
                        document.querySelector('form[action*="finish"]')?.submit();
                        return;
                    }
                    this.seconds--;
                    setTimeout(() => this.tick(), 1000);
                }
            }));

            // ─── Exam App ───
            Alpine.data('examApp', (sessionId, questionIds, categories) => ({
                sessionId: sessionId,
                questionIds: questionIds,
                categories: categories,
                totalQuestions: questionIds.length,
                currentIndex: 0,
                answers: {},
                answeredSet: {},

                get currentQuestion() {
                    return window._allQuestions?.[this.questionIds[this.currentIndex]] || null;
                },
                get currentCategory() {
                    return this.categories[this.currentIndex] || '';
                },
                get currentAnswer() {
                    return this.answers[this.questionIds[this.currentIndex]] || null;
                },
                get answeredCount() {
                    return Object.keys(this.answeredSet).length;
                },

                init() {
                    window._allQuestions = @json($questions->keyBy('id'));
                    // Load existing answers
                    const existing = @json($session->answers()->pluck('answer_text', 'question_id'));
                    this.answers = Object.assign({}, existing);
                    for (const [qid, val] of Object.entries(existing)) {
                        if (val !== null && val !== '') this.answeredSet[qid] = true;
                    }
                },

                isAnswered(idx) {
                    const qid = this.questionIds[idx];
                    return !!this.answeredSet[qid];
                },

                goTo(idx) {
                    if (idx >= 0 && idx < this.totalQuestions) {
                        this.currentIndex = idx;
                    }
                },

                nextQuestion() {
                    if (this.currentIndex < this.totalQuestions - 1) {
                        this.currentIndex++;
                    }
                },

                prevQuestion() {
                    if (this.currentIndex > 0) {
                        this.currentIndex--;
                    }
                },

                async saveCurrent(answer) {
                    const qid = this.questionIds[this.currentIndex];
                    this.answers[qid] = answer;
                    this.answeredSet[qid] = true;

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
                        if (data.error) alert(data.error);
                        if (data.answered_ids) {
                            this.answeredSet = {};
                            data.answered_ids.forEach(id => this.answeredSet[id] = true);
                        }
                    } catch(e) { console.error(e); }
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>
