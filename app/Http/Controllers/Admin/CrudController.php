<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $item->update($request->only($this->fillable));

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
