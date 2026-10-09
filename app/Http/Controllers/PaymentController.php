<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index()
    {
        return Inertia::render('Payments/Index', [
            'payments' => Payment::withCount(['orders', 'productPayments'])->latest()->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Payments/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:payments,name'],
            'is_active' => ['required', 'boolean'],
        ]);

        Payment::create($data);
        return redirect()->route('payments.index')->with('success', 'Payment method created successfully!');
    }

    public function edit(Payment $payment)
    {
        return Inertia::render('Payments/Edit', ['payment' => $payment]);
    }

    public function update(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('payments', 'name')->ignore($payment->id)],
            'is_active' => ['required', 'boolean'],
        ]);

        $payment->update($data);
        return redirect()->route('payments.index')->with('success', 'Payment method updated successfully!');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return redirect()->route('payments.index')->with('success', 'Payment method deleted successfully!');
    }
}
