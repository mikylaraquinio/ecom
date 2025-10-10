<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\XenditService;

class PaymentController extends Controller
{
    protected $xendit;

    public function __construct(XenditService $xendit)
    {
        $this->xendit = $xendit;
    }

    public function createInvoice(Request $request)
    {
        $data = [
            'external_id' => 'invoice-' . time(),
            'payer_email' => $request->email,
            'description' => 'Test Invoice',
            'amount' => $request->amount,
        ];

        $invoice = $this->xendit->createInvoice($data);
        return redirect($invoice['invoice_url']);
    }
}
