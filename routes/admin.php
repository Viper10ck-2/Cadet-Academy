<?php

use App\Http\Controllers\Admin\CrudController;
use App\Models\{Guardian, SchoolClass, Schedule, Material, Assignment, Certificate,
    Invoice, Payment, Lead, Campaign, Broadcast, Testimonial,
    BlogPost, Faq, Banner, Setting, AuditLog, Official};
use Illuminate\Support\Facades\Route;

// Macro to register CRUD routes
if (!function_exists('crudRoutes')) {
function crudRoutes(string $prefix, string $model, array $fillable, array $columns, string $title): void
{
    Route::get($prefix, function () use ($model, $fillable, $columns, $title, $prefix) {
        $ctrl = new CrudController();
        $ctrl->modelClass = $model;
        $ctrl->viewPrefix = $prefix;
        $ctrl->routePrefix = 'admin.' . str_replace('/', '.', $prefix);
        $ctrl->fillable = $fillable;
        $ctrl->listColumns = $columns;
        $ctrl->title = $title;
        return app()->call([$ctrl, 'index']);
    })->name(str_replace('/', '.', $prefix) . '.index');

    Route::get($prefix . '/create', function () use ($model, $fillable, $columns, $title, $prefix) {
        $ctrl = new CrudController();
        $ctrl->modelClass = $model;
        $ctrl->routePrefix = 'admin.' . str_replace('/', '.', $prefix);
        $ctrl->fillable = $fillable;
        $ctrl->listColumns = $columns;
        $ctrl->title = $title;
        return app()->call([$ctrl, 'create']);
    })->name(str_replace('/', '.', $prefix) . '.create');

    Route::post($prefix, function () use ($model, $fillable, $columns, $title, $prefix) {
        $ctrl = new CrudController();
        $ctrl->modelClass = $model;
        $ctrl->routePrefix = 'admin.' . str_replace('/', '.', $prefix);
        $ctrl->fillable = $fillable;
        $ctrl->listColumns = $columns;
        $ctrl->title = $title;
        return app()->call([$ctrl, 'store']);
    })->name(str_replace('/', '.', $prefix) . '.store');

    Route::get($prefix . '/{id}/edit', function ($id) use ($model, $fillable, $columns, $title, $prefix) {
        $ctrl = new CrudController();
        $ctrl->modelClass = $model;
        $ctrl->routePrefix = 'admin.' . str_replace('/', '.', $prefix);
        $ctrl->fillable = $fillable;
        $ctrl->listColumns = $columns;
        $ctrl->title = $title;
        return app()->call([$ctrl, 'edit'], ['id' => $id]);
    })->name(str_replace('/', '.', $prefix) . '.edit');

    Route::put($prefix . '/{id}', function ($id) use ($model, $fillable, $columns, $title, $prefix) {
        $ctrl = new CrudController();
        $ctrl->modelClass = $model;
        $ctrl->routePrefix = 'admin.' . str_replace('/', '.', $prefix);
        $ctrl->fillable = $fillable;
        $ctrl->listColumns = $columns;
        $ctrl->title = $title;
        return app()->call([$ctrl, 'update'], ['id' => $id]);
    })->name(str_replace('/', '.', $prefix) . '.update');

    Route::delete($prefix . '/{id}', function ($id) use ($model, $fillable, $columns, $title, $prefix) {
        $ctrl = new CrudController();
        $ctrl->modelClass = $model;
        $ctrl->routePrefix = 'admin.' . str_replace('/', '.', $prefix);
        $ctrl->fillable = $fillable;
        $ctrl->listColumns = $columns;
        $ctrl->title = $title;
        return app()->call([$ctrl, 'destroy'], ['id' => $id]);
    })->name(str_replace('/', '.', $prefix) . '.destroy');
}

} // end if function_exists

// 👨‍🎓 Akademik
crudRoutes('akademik/orang-tua', Guardian::class,
    ['name', 'email', 'phone', 'relation', 'address', 'occupation'],
    ['name' => 'Nama', 'email' => 'Email', 'phone' => 'Telepon', 'relation' => 'Hubungan'], 'Orang Tua');

crudRoutes('akademik/kelas', SchoolClass::class,
    ['name', 'code', 'description', 'capacity', 'is_active'],
    ['name' => 'Nama', 'code' => 'Kode', 'capacity' => 'Kapasitas', 'is_active' => 'Aktif'], 'Kelas');

crudRoutes('akademik/jadwal', Schedule::class,
    ['class_id', 'day', 'start_time', 'end_time', 'room'],
    ['class_id' => 'Kelas', 'day' => 'Hari', 'start_time' => 'Mulai', 'end_time' => 'Selesai', 'room' => 'Ruangan'], 'Jadwal');

crudRoutes('akademik/materi', Material::class,
    ['class_id', 'title', 'description', 'file_path', 'order'],
    ['title' => 'Judul', 'class_id' => 'Kelas', 'order' => 'Urutan'], 'Materi');

crudRoutes('akademik/tugas', Assignment::class,
    ['class_id', 'title', 'description', 'due_date', 'max_score'],
    ['title' => 'Judul', 'class_id' => 'Kelas', 'due_date' => 'Deadline', 'max_score' => 'Nilai Max'], 'Tugas');

crudRoutes('akademik/sertifikat', Certificate::class,
    ['title', 'description'],
    ['title' => 'Judul', 'description' => 'Deskripsi'], 'Sertifikat');

// 💳 Keuangan
crudRoutes('keuangan/tagihan', Invoice::class,
    ['student_id', 'description', 'amount', 'due_date', 'status'],
    ['invoice_number' => 'No. Invoice', 'student_id' => 'Siswa', 'amount' => 'Jumlah', 'status' => 'Status', 'due_date' => 'Jatuh Tempo'], 'Tagihan');

crudRoutes('keuangan/pembayaran', Payment::class,
    ['invoice_id', 'amount', 'method', 'reference', 'status'],
    ['payment_number' => 'No. Pembayaran', 'invoice_id' => 'Invoice', 'amount' => 'Jumlah', 'method' => 'Metode', 'status' => 'Status'], 'Pembayaran');

// Alias: redirect legacy invoice route
Route::redirect('/admin/keuangan/invoice', '/admin/keuangan/tagihan')->name('admin.keuangan.invoice.index');

// 📢 CRM
crudRoutes('crm/leads', Lead::class,
    ['name', 'email', 'phone', 'source', 'status', 'notes'],
    ['name' => 'Nama', 'email' => 'Email', 'phone' => 'Telepon', 'source' => 'Sumber', 'status' => 'Status'], 'Leads');

crudRoutes('crm/kampanye', Campaign::class,
    ['name', 'type', 'content', 'status'],
    ['name' => 'Nama', 'type' => 'Tipe', 'status' => 'Status'], 'Kampanye');

crudRoutes('crm/broadcast', Broadcast::class,
    ['title', 'type', 'status'],
    ['title' => 'Judul', 'type' => 'Tipe', 'status' => 'Status'], 'Broadcast');

crudRoutes('crm/testimoni', Testimonial::class,
    ['name', 'role', 'content', 'rating', 'is_published', 'order'],
    ['name' => 'Nama', 'role' => 'Role', 'rating' => 'Rating', 'is_published' => 'Publikasi'], 'Testimoni');

// 🌐 Website
crudRoutes('website/blog', BlogPost::class,
    ['title', 'slug', 'excerpt', 'content', 'status'],
    ['title' => 'Judul', 'slug' => 'Slug', 'status' => 'Status'], 'Blog');

crudRoutes('website/faq', Faq::class,
    ['question', 'answer', 'category', 'order', 'is_published'],
    ['question' => 'Pertanyaan', 'category' => 'Kategori', 'order' => 'Urutan', 'is_published' => 'Aktif'], 'FAQ');

crudRoutes('website/banner', Banner::class,
    ['title', 'subtitle', 'image_path', 'link_url', 'is_active', 'order'],
    ['title' => 'Judul', 'image_path' => 'Gambar', 'is_active' => 'Aktif', 'order' => 'Urutan'], 'Banner');

// ⚙ Pengaturan
crudRoutes('settings/smtp', Setting::class,
    ['key', 'value', 'label'],
    ['key' => 'Key', 'value' => 'Value', 'label' => 'Label'], 'SMTP');

crudRoutes('settings/payment-gateway', Setting::class,
    ['key', 'value', 'label'],
    ['key' => 'Key', 'value' => 'Value', 'label' => 'Label'], 'Payment Gateway');

crudRoutes('settings/audit-log', AuditLog::class,
    ['action', 'model_type', 'ip_address'],
    ['user_id' => 'User', 'action' => 'Aksi', 'model_type' => 'Model', 'created_at' => 'Waktu'], 'Audit Log');

// 👥 Pejabat
crudRoutes('website/pejabat', Official::class,
    ['name', 'position', 'photo', 'order', 'is_active'],
    ['name' => 'Nama', 'position' => 'Jabatan', 'order' => 'Urutan', 'is_active' => 'Aktif'], 'Pejabat');
