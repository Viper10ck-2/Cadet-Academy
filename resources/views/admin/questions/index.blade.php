@extends('layouts.admin')
@section('title', 'Bank Soal')
@section('content')

<script>
// Global question data for edit references
window._questionsData = {!! Js::from($questions->keyBy('id')) !!};

/** Terapkan data list dari response AJAX langsung ke DOM */
function applyListData(data) {
    if (!data.html) return;
    const container = document.getElementById('bankSoalContainer');
    if (container) container.innerHTML = data.html;
    if (data.questionsData) window._questionsData = data.questionsData;
    attachHandlers();
}

document.addEventListener('alpine:init', () => {
    Alpine.data('questionBank', () => ({
        openModal: false,
        step: 1,
        editId: null,
        submitting: false,
        form: {
            category: '',
            type: 'multiple_choice',
            question_text: '',
            options: { A: '', B: '', C: '', D: '' },
            correct_answer: '',
            points: 1,
        },

        resetForm() {
            this.step = 1;
            this.editId = null;
            this.submitting = false;
            this.form = {
                category: '',
                type: 'multiple_choice',
                question_text: '',
                options: { A: '', B: '', C: '', D: '' },
                correct_answer: '',
                points: 1,
            };
        },

        initEdit(id) {
            const q = window._questionsData[id];
            if (!q) return;
            this.editId = id;
            this.form.category = q.category || '';
            this.form.type = q.type || 'multiple_choice';
            this.form.question_text = q.question_text || '';
            this.form.options = q.options || { A: '', B: '', C: '', D: '' };
            this.form.correct_answer = q.correct_answer || '';
            this.form.points = q.points || 1;
            this.step = 2;
            this.openModal = true;
        },

        async submitForm(e) {
            if (this.submitting) return;
            this.submitting = true;

            const isEdit = !!this.editId;
            const activeCategory = new URLSearchParams(window.location.search).get('category') || '';
            let url = isEdit
                ? '{{ url('admin/questions') }}/' + this.editId
                : '{{ route('admin.questions.store') }}';
            if (activeCategory) url += (url.includes('?') ? '&' : '?') + 'category=' + activeCategory;

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            if (isEdit) formData.append('_method', 'PUT');
            formData.append('category', this.form.category);
            formData.append('type', this.form.type);
            formData.append('question_text', this.form.question_text);
            formData.append('correct_answer', this.form.correct_answer);
            formData.append('points', this.form.points);
            if (this.form.type === 'multiple_choice') {
                Object.entries(this.form.options).forEach(([k, v]) => formData.append('options[' + k + ']', v));
            }

            try {
                const res = await fetch(url, { method: 'POST', headers: { 'Accept': 'application/json' }, body: formData });
                const data = await res.json();
                if (data.success) {
                    applyListData(data);
                    this.openModal = false;
                    this.resetForm();
                }
            } catch(e) {
                console.error(e);
            }
            this.submitting = false;
        },
    }));
});

function attachHandlers() {
    const container = document.getElementById('bankSoalContainer');
    if (!container) return;
    // Pagination links → AJAX
    container.querySelectorAll('a[href*="page="]').forEach(link => {
        link.addEventListener('click', async function(e) {
            e.preventDefault();
            const pageUrl = new URL(this.href);
            pageUrl.searchParams.set('ajax', '1');
            const r = await fetch(pageUrl, { headers: { 'Accept': 'application/json' } });
            const d = await r.json();
            applyListData(d);
            container.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
}

// Called from list items
function editQuestion(id) {
    const el = document.querySelector('[x-data="questionBank"]');
    if (!el || !el._x_dataStack) return;
    const alpine = el._x_dataStack[0];
    if (alpine) alpine.initEdit(id);
}

async function deleteQuestion(id) {
    if (!confirm('Hapus soal ini dari Bank Soal?')) return;
    try {
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'DELETE');

        // Kirim category yang sedang aktif dari URL saat ini
        const activeCategory = new URLSearchParams(window.location.search).get('category') || '';
        const url = new URL('{{ url('admin/questions') }}/' + id);
        if (activeCategory) url.searchParams.set('category', activeCategory);

        const res = await fetch(url, {
            method: 'POST',
            headers: { 'Accept': 'application/json' },
            body: formData,
        });
        const data = await res.json();
        if (data.success) {
            applyListData(data);
        }
    } catch(e) { console.error(e); }
}

// Stats filter: AJAX navigation
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.stats-filter').forEach(link => {
        link.addEventListener('click', async function(e) {
            e.preventDefault();
            const filterUrl = new URL(this.href);
            filterUrl.searchParams.set('ajax', '1');
            history.pushState({}, '', this.href);
            const r = await fetch(filterUrl, { headers: { 'Accept': 'application/json' } });
            const d = await r.json();
            applyListData(d);
        });
    });
    attachHandlers();
});
</script>

<div x-data="questionBank">
<div class="flex items-center justify-between mb-3">
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-white">Bank Soal</h1>
    </div>
    <button @click="openModal = true; step = 1; resetForm()"
            class="inline-flex items-center gap-1.5 px-3 py-2 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 shadow-sm shadow-indigo-500/20 transition-all">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Soal
    </button>
</div>

{{-- Container yg di-refresh via AJAX --}}
<div id="bankSoalContainer">
    @include('admin.questions._list', ['questions' => $questions, 'counts' => $counts])
</div>

<!-- ======================== MODAL ======================== -->
<div x-cloak x-show="openModal" @click="openModal = false; resetForm()"
     class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm transition-opacity"
     x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
</div>

<div x-cloak x-show="openModal"
     class="fixed inset-0 z-50 flex items-start justify-center pt-[10vh] px-4 overflow-y-auto"
     x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
     x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl shadow-black/20 w-full max-w-2xl border border-gray-100 dark:border-gray-700" @click.stop>

        <!-- STEP 1 -->
        <template x-if="step === 1">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Tambah Soal</h3>
                    <button @click="openModal = false; resetForm()" class="text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Pilih tipe soal yang ingin ditambahkan:</p>
                <div class="grid grid-cols-2 gap-4">
                    @foreach([['TIU','🧠','Tes Intelegensi Umum','blue'],['TWK','🇮🇩','Tes Wawasan Kebangsaan','red'],['TKP','🎯','Tes Karakteristik Pribadi','green'],['TBI','📖','Tes Bahasa Inggris','amber']] as [$cat,$icon,$desc,$color])
                    <button @click="form.category = '{{ $cat }}'; step = 2"
                            class="p-6 rounded-xl border-2 border-gray-100 dark:border-gray-700 hover:border-{{ $color }}-400 hover:bg-{{ $color }}-50/30 dark:hover:bg-{{ $color }}-900/10 dark:hover:border-{{ $color }}-500 transition-all text-center group">
                        <div class="w-14 h-14 rounded-2xl bg-{{ $color }}-100 dark:bg-{{ $color }}-900/30 text-{{ $color }}-600 dark:text-{{ $color }}-400 flex items-center justify-center mx-auto mb-3 text-2xl font-bold group-hover:scale-110 transition-transform">{{ $icon }}</div>
                        <h4 class="font-bold text-gray-900 dark:text-white text-lg">{{ $cat }}</h4>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $desc }}</p>
                    </button>
                    @endforeach
                </div>
            </div>
        </template>

        <!-- STEP 2 -->
        <template x-if="step === 2">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <button @click="step = 1" class="text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white" x-text="editId ? 'Edit Soal' : 'Tambah Soal'"></h3>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                              :class="{
                                  'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400': form.category === 'TIU',
                                  'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': form.category === 'TWK',
                                  'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': form.category === 'TKP',
                                  'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400': form.category === 'TBI',
                              }" x-text="form.category"></span>
                    </div>
                    <button @click="openModal = false; resetForm()" class="text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <input type="hidden" name="category" x-model="form.category">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tipe Soal</label>
                        <select name="type" x-model="form.type" @change="if ($event.target.value === 'essay') { form.options = {}; form.correct_answer = '' }"
                                class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="multiple_choice">Pilihan Ganda</option>
                            <option value="essay">Essay</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Pertanyaan</label>
                        <textarea name="question_text" x-model="form.question_text" rows="3" required placeholder="Tulis teks pertanyaan di sini..."
                                  class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>

                    <div x-show="form.type === 'multiple_choice'">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Opsi Jawaban</label>
                        <div class="grid grid-cols-2 gap-3">
                            <template x-for="opt in ['A', 'B', 'C', 'D']" :key="opt">
                                <div>
                                    <label class="text-xs text-gray-400 mb-1 block" x-text="'Opsi ' + opt"></label>
                                    <input type="text" :name="'options[' + opt + ']'" x-model="form.options[opt]"
                                           :placeholder="'Pilihan ' + opt"
                                           class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                <span x-text="form.type === 'multiple_choice' ? 'Kunci Jawaban (A/B/C/D)' : 'Jawaban Referensi'"></span>
                            </label>
                            <input type="text" name="correct_answer" x-model="form.correct_answer" required
                                   :placeholder="form.type === 'multiple_choice' ? 'Contoh: A' : 'Tulis jawaban referensi...'"
                                   class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Poin</label>
                            <input type="number" name="points" x-model="form.points" min="1" required
                                   class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="openModal = false; resetForm()"
                                class="px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            Batal
                        </button>
                        <button type="button" @click="submitForm()" :disabled="submitting"
                                class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 shadow-sm shadow-indigo-500/20 transition-all disabled:opacity-50"
                                x-text="editId ? 'Simpan Perubahan' : 'Tambah Soal'"></button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

</div>{{-- End x-data --}}

@endsection
