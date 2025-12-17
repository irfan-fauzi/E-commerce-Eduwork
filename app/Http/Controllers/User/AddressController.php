<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index()
    {
        // Ambil alamat langsung lewat model untuk menghindari peringatan "Undefined method 'addresses'"
        $addresses = Address::where('user_id', Auth::id())->get();
        return view('user.addresses.index', compact('addresses'));
    }

    public function create()
    {
        return view('user.addresses.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_name' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:30',
            'label' => 'nullable|string|max:100',
            'address_text' => 'required|string|max:500',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'is_primary' => 'boolean',
        ]);

        $validated['user_id'] = Auth::id();

        if ($validated['is_primary'] ?? false) {
            // Non-aktifkan primary address lain milik user
            Address::where('user_id', Auth::id())->update(['is_primary' => false]);
        }

        Address::create($validated);
        return redirect()->route('user.addresses.index')->with('success', 'Address added.');
    }

    public function edit(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        return view('user.addresses.form', compact('address'));
    }

    public function update(Request $request, Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
              'recipient_name' => 'nullable|string|max:150',
                        'phone' => 'nullable|string|max:30',
            'label' => 'nullable|string|max:100',
            'address_text' => 'required|string|max:500',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'is_primary' => 'boolean',
        ]);

        if ($validated['is_primary'] ?? false) {
            // Non-aktifkan primary address lain milik user
            Address::where('user_id', Auth::id())->update(['is_primary' => false]);
        }

        $address->update($validated);
        return redirect()->route('user.addresses.index')->with('success', 'Address updated.');
    }

    public function destroy(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        $address->delete();
        return redirect()->route('user.addresses.index')->with('success', 'Address deleted.');
    }
}
