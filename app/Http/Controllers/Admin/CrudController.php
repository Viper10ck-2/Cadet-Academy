<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CrudController extends Controller
{
    public string $modelClass = '';
    public string $viewPrefix = '';
    public string $routePrefix = '';
    public array $fillable = [];
    public array $listColumns = [];
    public string $title = '';

    public function index(Request $request): View
    {
        $query = $this->modelClass::query()->latest();
        if ($request->filled('search') && method_exists($this->modelClass, 'scopeSearch')) {
            $query->search($request->search);
        }
        $items = $query->paginate(15)->withQueryString();

        return view("admin.crud.index", [
            'items' => $items,
            'title' => $this->title,
            'columns' => $this->listColumns,
            'routePrefix' => $this->routePrefix,
            'viewPrefix' => $this->viewPrefix,
        ]);
    }

    public function create(): View
    {
        return view("admin.crud.form", [
            'item' => null,
            'title' => $this->title,
            'fillable' => $this->fillable,
            'routePrefix' => $this->routePrefix,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->only($this->fillable);
        
        // Handle file uploads for photo/image fields
        foreach ($this->fillable as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('uploads', 'public');
            }
        }
        
        $this->modelClass::create($data);

        return redirect()->route($this->routePrefix . '.index')
            ->with('status', 'Data berhasil ditambahkan!');
    }

    public function edit(int $id): View
    {
        $item = $this->modelClass::findOrFail($id);

        return view("admin.crud.form", [
            'item' => $item,
            'title' => $this->title,
            'fillable' => $this->fillable,
            'routePrefix' => $this->routePrefix,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $item = $this->modelClass::findOrFail($id);
        $data = $request->only($this->fillable);
        
        // Handle file uploads for photo/image fields only
        $fileFields = ['photo', 'image', 'avatar', 'file_path', 'file', 'path', 'proof_path'];
        foreach ($this->fillable as $field) {
            if (!in_array($field, $fileFields) && !Str::contains($field, ['photo', 'image', 'avatar', 'file', 'path'])) {
                continue;
            }
            if ($request->hasFile($field)) {
                // Delete old file if exists
                if ($item->$field && \Storage::disk('public')->exists($item->$field)) {
                    \Storage::disk('public')->delete($item->$field);
                }
                $data[$field] = $request->file($field)->store('uploads', 'public');
            } else {
                // Keep old value, remove from update data
                unset($data[$field]);
            }
        }
        
        $item->update($data);

        return redirect()->route($this->routePrefix . '.index')
            ->with('status', 'Data berhasil diperbarui!');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->modelClass::findOrFail($id)->delete();

        return redirect()->route($this->routePrefix . '.index')
            ->with('status', 'Data berhasil dihapus!');
    }
}
