<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\GownCollection;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('can:admin');
    // }

    // List all payments (admin side)
    public function index(Request $request)
    {
        $q      = trim((string)$request->input('search', ''));
        $status = $request->input('status'); // paid|pending|failed|null

        $list = \App\Models\Payment::with(['user:id,name,email', 'gownCollection:id,size'])
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($qq) use ($q) {
                    $qq->where('reference', 'like', "%{$q}%")
                        ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%"));
                });
            })
            ->when($status, fn($qb) => $qb->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $payments = $list->getCollection()->map(fn($p) => [
            'id'         => $p->id,
            'reference'  => $p->reference,
            'student'    => $p->user?->name ?? '—',
            'gown'       => $p->gownCollection?->size ?? '—',
            'amount'     => (float) $p->amount,
            'status'     => $p->status,
            'created_at' => $p->created_at->toDateTimeString(),
        ]);

        return \Inertia\Inertia::render('admin/Payment/Index', [
            'payments'   => $payments,
            'pagination' => [
                'current_page' => $list->currentPage(),
                'last_page'    => $list->lastPage(),
                'per_page'     => $list->perPage(),
                'total'        => $list->total(),
            ],
            'filters'    => $request->only(['search', 'status']),
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

    public function export(): StreamedResponse
{
    $callback = function () {
        $out = fopen('php://output','w');
        fputcsv($out, ['ID','Reference','Student','Gown Size','Amount','Status','Created At']);
        \App\Models\Payment::with(['user:id,name','gownCollection:id,size'])
          ->orderBy('id')
          ->chunk(1000, function ($rows) use ($out) {
            foreach ($rows as $p) {
              fputcsv($out, [
                $p->id,$p->reference,$p->user?->name,$p->gownCollection?->size,
                number_format((float)$p->amount,2,'.',''),$p->status,$p->created_at->toDateTimeString()
              ]);
            }
          });
        fclose($out);
    };

    return response()->streamDownload($callback, 'payments.csv', [
        'Content-Type' => 'text/csv; charset=UTF-8',
        'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
    ]);
}
}
