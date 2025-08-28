<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\GownCollection;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    // List all payments (admin side)
    public function index()
    {
        $payments = Payment::with(['user', 'gownCollection'])->get();

        return Inertia::render('admin/Payment/Index', [
            'payments' => $payments
        ]);
    }

    // Show create payment page (student side)
    public function create($gownId)
    {
        $gown = GownCollection::findOrFail($gownId);

        return Inertia::render('student/Payment/Create', [
            'gown' => $gown
        ]);
    }

    // Store payment (simulate success)
    public function store(Request $request)
    {
        $payment = Payment::create([
            'user_id' => auth()->id(),
            'gown_collection_id' => $request->gown_id,
            'amount' => $request->amount,
            'status' => 'paid',
            'reference' => 'INV-' . strtoupper(uniqid()),
        ]);

        return redirect()->route('student.payments.receipt', $payment->id)
            ->with('message', 'Payment successful!');
    }

    // Show receipt
    public function show($id)
    {
        $payment = Payment::with(['user', 'gownCollection'])->findOrFail($id);

        return Inertia::render('student/Payments/Receipt', [
            'payment' => $payment
        ]);
    }
}
