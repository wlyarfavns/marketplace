<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminBrandController extends Controller
{
    public function index()
    {
        $brands = Brand::withCount('products')->orderBy('homepage_order', 'asc')->paginate(15);

        return view('admin.brands.index', compact('brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'dp_percentage' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'homepage_order' => 'nullable|integer',
            'show_on_homepage' => 'nullable|boolean',
            'logo' => 'nullable|image|max:2048',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('brands', 'public');
        }

        Brand::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'dp_percentage' => $validated['dp_percentage'],
            'description' => $validated['description'] ?? null,
            'homepage_order' => $validated['homepage_order'] ?? 0,
            'show_on_homepage' => ! empty($validated['show_on_homepage']),
            'logo_path' => $logoPath,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Brand baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'dp_percentage' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'homepage_order' => 'nullable|integer',
            'show_on_homepage' => 'nullable|boolean',
            'is_active' => 'required|boolean',
            'logo' => 'nullable|image|max:2048',
        ]);

        $data = [
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'dp_percentage' => $validated['dp_percentage'],
            'description' => $validated['description'] ?? null,
            'homepage_order' => $validated['homepage_order'] ?? 0,
            'show_on_homepage' => ! empty($validated['show_on_homepage']),
            'is_active' => $validated['is_active'],
        ];

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('brands', 'public');
        }

        $brand->update($data);

        return redirect()->back()->with('success', 'Data Brand berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);
        $brand->delete();

        return redirect()->back()->with('success', 'Brand berhasil dihapus.');
    }
}
