<?php

namespace Mariojgt\Gateway\Controllers;

use Carbon\Carbon;
use Stripe\StripeClient;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Braintree\Gateway;

/**
 * This Controller comes with the basic for a checkout out of the box
 * More fuction you need to extend and implement for you needs
 */
class BraintreeController extends Controller
{

    public $gateway;

    /**
     * Start the controller with all the information read to be use
     */
    public function __construct()
    {
        // Start the gateway
        $this->gateway = new Gateway([
            'environment' => config('braintree.braintree_environment'),
            'merchantId'  => config('braintree.braintree_merchantId'),
            'publicKey'   => config('braintree.braintree_publicKey'),
            'privateKey'  => config('braintree.braintree_privateKey'),
        ]);
    }

    /**
     *
     * Generate a client token so we can take paymentes
     *
     * @return [type]
     */
    public function clientToken()
    {
        return $this->gateway->clientToken()->generate();
    }

    /**
     * Get braintree order information
     *
     * @param mixed $transaction_id
     *
     * @return [type]
     */
    public function orderInfo($transaction_id)
    {
        return $this->gateway->transaction()->find($transaction_id);
    }

    /**
     * Create a payment transaction based in the array
     * @param array $paymentArray
     *
     * @return [type]
     */
    public function payTransaction(array $paymentArray)
    {
        // Example payment array
        // [
        //     'amount'             => ($finalCart['total_price']['total_price_inc_tax'] + $postage->total_price['default']['price_inc_tax']),
        //     'paymentMethodNonce' => Request('payment_method_nonce'),
        //     'deviceData'         => Request('client_data'),
        //     'customer'           => [
        //         'firstName' => $orderInfo['user']->first_name,
        //         'lastName'  => $orderInfo['user']->last_name,
        //         'company'   => $orderInfo['company']->name ?? 'undefine',
        //         'phone'     => $orderInfo['address']->phone,
        //         'fax'       => $orderInfo['address']->phone,
        //         'email'     => $orderInfo['user']->email
        //     ],
        //     'billing' => [
        //         'firstName'         => $orderInfo['user']->first_name,
        //         'lastName'          => $orderInfo['user']->last_name,
        //         'company'           => $orderInfo['company']->name ?? 'undefine',
        //         'streetAddress'     => $orderInfo['address']->address,
        //         'extendedAddress'   => $orderInfo['address']->address2,
        //         'locality'          => $orderInfo['address']->town,
        //         'region'            => $orderInfo['address']->county,
        //         'postalCode'        => $orderInfo['address']->postcode,
        //         'countryCodeAlpha2' => $orderInfo['address']->country->iso_code2
        //     ],
        //     'shipping' => [
        //         'firstName'         => $orderInfo['user']->first_name,
        //         'lastName'          => $orderInfo['user']->last_name,
        //         'company'           => $orderInfo['company']->name ?? 'undefine',
        //         'streetAddress'     => $orderInfo['address']->address,
        //         'extendedAddress'   => $orderInfo['address']->address2,
        //         'locality'          => $orderInfo['address']->town,
        //         'region'            => $orderInfo['address']->county,
        //         'postalCode'        => $orderInfo['address']->postcode,
        //         'countryCodeAlpha2' => $orderInfo['address']->country->iso_code2
        //     ],
        //     'options'            => [
        //         'submitForSettlement' => true
        //     ]
        // ]
        // Create a payment in the braintree
        $transaction = $this->gateway->transaction()->sale($paymentArray);
        // Create a log
        $this->createLog($transaction);
        // Return the object
        return $transaction;
    }

    /**
     * Create a stripe log file when the session has been created
     * @param mixed $data
     *
     */
    public function createLog($data)
    {
        $LogFileName = $data->transaction->id . '_payment.log';
        Storage::put(
            'braintree/'.config('gateway.braintree_log') . $LogFileName, json_encode($data->transaction)
        );
    }
}
