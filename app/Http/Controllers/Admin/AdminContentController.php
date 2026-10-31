<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminHandle;
use App\Models\BankAccount;
use App\Models\Banner;
use App\Models\CustomerService;
use App\Models\Feature;
use App\Models\SocialMedia;
use Illuminate\Http\Request;

class AdminContentController extends Controller
{
    // 1. Banner Management
    public function banners()
    {
        $banners = Banner::orderBy('sort_order', 'asc')->get();

        return view('admin.content.banners', compact('banners'));
    }

    public function storeBanner(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'url' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'image' => 'required|image|max:3072',
        ]);

        $imagePath = $request->file('image')->store('banners', 'public');

        Banner::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'url' => $validated['url'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'image_path' => $imagePath,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Banner promosi baru berhasil ditambahkan.');
    }

    public function destroyBanner($id)
    {
        Banner::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Banner berhasil dihapus.');
    }

    // 2. Keunggulan / Features
    public function features()
    {
        $features = Feature::orderBy('sort_order', 'asc')->get();

        return view('admin.content.features', compact('features'));
    }

    public function storeFeature(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'sort_order' => 'nullable|integer',
        ]);

        Feature::create(array_merge($validated, ['is_active' => true]));

        return redirect()->back()->with('success', 'Pilar keunggulan berhasil ditambahkan.');
    }

    public function destroyFeature($id)
    {
        Feature::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Keunggulan berhasil dihapus.');
    }

    // 3. Social Media
    public function socials()
    {
        $socials = SocialMedia::orderBy('sort_order', 'asc')->get();

        return view('admin.content.socials', compact('socials'));
    }

    public function storeSocial(Request $request)
    {
        $validated = $request->validate([
            'platform' => 'required|string|max:50',
            'url' => 'required|url',
            'icon_class' => 'nullable|string',
            'brand_color' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        SocialMedia::create(array_merge($validated, ['is_active' => true]));

        return redirect()->back()->with('success', 'Tautan media sosial berhasil disimpan.');
    }

    public function destroySocial($id)
    {
        SocialMedia::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Media sosial berhasil dihapus.');
    }

    // 4. Customer Service (Floating WA)
    public function customerServices()
    {
        $services = CustomerService::latest()->get();

        return view('admin.content.customer-services', compact('services'));
    }

    public function storeCustomerService(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'is_floating_whatsapp' => 'nullable|boolean',
        ]);

        if (! empty($validated['is_floating_whatsapp'])) {
            // Tepat 1 nomor yang menjadi floating WA aktif
            CustomerService::query()->update(['is_floating_whatsapp' => false]);
        }

        CustomerService::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'is_floating_whatsapp' => ! empty($validated['is_floating_whatsapp']),
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Kontak Customer Service berhasil disimpan.');
    }

    public function setFloatingWa($id)
    {
        CustomerService::query()->update(['is_floating_whatsapp' => false]);
        $cs = CustomerService::findOrFail($id);
        $cs->update(['is_floating_whatsapp' => true]);

        return redirect()->back()->with('success', "Nomor {$cs->name} berhasil diatur sebagai Floating WhatsApp utama di website.");
    }

    public function destroyCustomerService($id)
    {
        CustomerService::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Kontak CS berhasil dihapus.');
    }

    // 5. Admin Handle
    public function handles()
    {
        $handles = AdminHandle::latest()->get();

        return view('admin.content.handles', compact('handles'));
    }

    public function storeHandle(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
        ]);

        AdminHandle::create(array_merge($validated, ['is_active' => true]));

        return redirect()->back()->with('success', 'Admin handle order berhasil didaftarkan.');
    }

    public function destroyHandle($id)
    {
        AdminHandle::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Admin handle berhasil dihapus.');
    }

    // 6. Bank Accounts
    public function banks()
    {
        $banks = BankAccount::latest()->get();

        return view('admin.content.banks', compact('banks'));
    }

    public function storeBank(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_holder_name' => 'required|string|max:255',
        ]);

        BankAccount::create(array_merge($validated, ['is_active' => true]));

        return redirect()->back()->with('success', 'Rekening bank tujuan transfer berhasil ditambahkan.');
    }

    public function destroyBank($id)
    {
        BankAccount::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Rekening bank berhasil dihapus.');
    }
}
