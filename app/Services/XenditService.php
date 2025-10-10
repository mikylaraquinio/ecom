<?php

namespace App\Services;

use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\Transfer\TransferApi;
use Xendit\Transfer\CreateTransferRequest;
use Xendit\Platform\PlatformApi;
use Xendit\Platform\CreateAccountRequest;

class XenditService
{
    protected $config;

    public function __construct()
    {
        $this->config = Configuration::getDefaultConfiguration();
        $this->config->setApiKey(env('XENDIT_SECRET_KEY'));
    }

    /**
     * Create an invoice for a buyer
     */
    public function createInvoice(array $data)
    {
        $apiInstance = new InvoiceApi(null, $this->config);

        $params = new CreateInvoiceRequest([
            'external_id' => $data['external_id'],
            'payer_email' => $data['payer_email'] ?? 'customer@example.com',
            'description' => $data['description'],
            'amount' => (float) $data['amount'],
            'success_redirect_url' => $data['success_redirect_url'],
            'failure_redirect_url' => $data['failure_redirect_url'],
            'payment_methods' => ['GCASH', 'GRABPAY', 'PAYMAYA', 'QRPH'],
        ]);

        return $apiInstance->createInvoice($params);
    }

    /**
     * Create a connected seller account (simulated for test mode)
     */
    public function createSellerAccount(string $email, string $businessName)
    {
        $apiInstance = new PlatformApi(null, $this->config);

        $accountRequest = new CreateAccountRequest([
            'email' => $email,
            'type' => 'OWNED',
            'business_profile' => [
                'business_name' => $businessName,
            ],
        ]);

        $account = $apiInstance->createAccount($accountRequest);
        return $account;
    }

    /**
     * Transfer payout to a seller’s connected account
     */
    public function transferToSeller(array $data)
    {
        $apiInstance = new TransferApi(null, $this->config);

        $transferRequest = new CreateTransferRequest([
            'reference' => $data['reference'],
            'amount' => (float) $data['amount'],
            'destination_user_id' => $data['seller_id'], // seller’s connected account
            'currency' => 'PHP',
            'description' => 'Payout for ' . $data['reference'],
        ]);

        return $apiInstance->createTransfer($transferRequest);
    }
}
