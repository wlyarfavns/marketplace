<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Statistik Belanja
        $totalOrders = Order::where('user_id', $user->id)->count();
        $activeOrders = Order::where('user_id', $user->id)->whereIn('order_status', ['pending', 'processing', 'shipped'])->count();
        $completedOrders = Order::where('user_id', $user->id)->where('order_status', 'completed')->count();
        $totalSpent = Order::where('user_id', $user->id)->where('payment_status', 'verified')->sum('grand_total');

        $recentOrders = Order::where('user_id', $user->id)
            ->with(['items'])
            ->latest()
            ->take(5)
            ->get();

        return view('account.dashboard', compact(
            'user', 'totalOrders', 'activeOrders', 'completedOrders', 'totalSpent', 'recentOrders'
        ));
    }

    public function orders()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
            ->with(['items.product', 'paymentConfirmations'])
            ->latest()
            ->paginate(10);

        return view('account.orders', compact('user', 'orders'));
    }

    public function orderDetail($orderNumber)
    {
        $user = Auth::user();
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', $user->id)
            ->with(['items.product', 'seller', 'coupon', 'paymentConfirmations.bankAccount', 'invoice'])
            ->firstOrFail();

        return view('account.order-detail', compact('order'));
    }

    public function profile()
    {
        $user = Auth::user();
        $sellers = Seller::active()->get();

        return view('account.profile', compact('user', 'sellers'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'default_seller_code' => 'nullable|string|max:50',
            'favorite_courier' => 'nullable|string|max:50',
        ]);

        $user->update($validated);

        return redirect()->back()->with('success', 'Profil dan preferensi akun Anda berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Kata sandi berhasil diubah.');
    }
}
