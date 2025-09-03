<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\GownCollection;
use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function create($gownId)
    {
        $gown = GownCollection::findOrFail($gownId);

        // Decide how you derive deposit amount (fixed/config/DB).
        $amount = config('convocation.deposit_amount', 100.00);

        // Keep the props you actually use in Vue
        return Inertia::render('student/Payments/Create', [
            'gown'   => [
                'id'   => $gown->id,
                'size' => $gown->size ?? null,
            ],
            'amount' => number_format($amount, 2, '.', ''),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'gown_id' => ['required', 'exists:gown_collections,id'],
            'amount'  => ['required', 'numeric', 'min:0'],
        ]);

        // Ensure the gown reservation belongs to the student and not already settled
        $gown = \App\Models\GownCollection::where('id', $data['gown_id'])
            ->where('user_id', $request->user()->id)
            ->lockForUpdate()
            ->first();

        if (!$gown) abort(403);

        // Idempotency: avoid multiple PAID records for same gown
        $alreadyPaid = \App\Models\Payment::where('user_id', $request->user()->id)
            ->where('gown_collection_id', $gown->id)
            ->where('status', 'paid')
            ->exists();

        if ($alreadyPaid) {
            throw ValidationException::withMessages([
                'gown_id' => 'Payment already recorded for this reservation.',
            ]);
        }

        $payment = DB::transaction(function () use ($request, $gown, $data) {
            return \App\Models\Payment::create([
                'user_id'            => $request->user()->id,
                'gown_collection_id' => $gown->id,
                'amount'             => $data['amount'],
                'status'             => 'paid', // mock gateway success
                'reference'          => 'INV-' . strtoupper(uniqid()),
            ]);
        });

        return redirect()->route('student.payments.receipt', $payment->id)
            ->with('message', 'Payment successful!');
    }
    public function show($id)
    {
        $payment = Payment::with('gownCollection')->findOrFail($id);
        $this->authorize('view', $payment);

        // Optional: ensure the student can only see own receipt
        if ($payment->user_id !== auth()->id()) abort(403);

        return Inertia::render('student/Payments/Receipt', [
            'payment' => [
                'id'         => $payment->id,
                'reference'  => $payment->reference,
                'amount'     => number_format($payment->amount, 2, '.', ''),
                'status'     => $payment->status,
                'created_at' => $payment->created_at->toDateTimeString(),
                'gown'       => $payment->gownCollection?->only(['id', 'size']),
            ],
        ]);
    }

    public function index()
    {
        $payments = \App\Models\Payment::with('gownCollection')
            ->where('user_id', auth()->id())
            ->latest()
            ->get()
            ->map(fn($p) => [
                'id'         => $p->id,
                'reference'  => $p->reference,
                'amount'     => number_format($p->amount, 2, '.', ''),
                'status'     => $p->status,
                'created_at' => $p->created_at->toDateTimeString(),
                'gown'       => $p->gownCollection?->only(['id', 'size']),
            ]);

        // If you can identify the student's current gown collection, pass it to enable the "Pay deposit" button.
        $gown = \App\Models\GownCollection::where('user_id', auth()->id())->latest()->first();

        return \Inertia\Inertia::render('student/Payments/Index', [
            'payments' => $payments,
            'canPay'   => (bool) $gown,
            'gownId'   => $gown?->id,
            'amount'   => number_format(config('convocation.deposit_amount', 100.00), 2, '.', ''),
        ]);
    }
}
