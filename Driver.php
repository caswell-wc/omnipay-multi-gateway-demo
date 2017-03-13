<?php
/**
 * This is an example of how you would use the objects
 */

$gatewayName = 'AuthorizeNet';
$credentials = new stdClass();
$credentials->login = 'login';
$credentials->key = 'key';

/**
 * Here you would have some logic to determine which gateway to use. We are using a factory class to accomplish this.
 */
/** @var \gateways\GatewayInterface|null $gateway */
$gateway = null;
switch ($gatewayName) {
    case 'AuthorizeNet':
        $gateway = new \gateways\AuthorizeNet($credentials);
        break;
    case 'FirstData':
        $gateway = new \gateways\FirstData($credentials);
        break;
    default:
        throw new Exception('bad gateway name');
}

/**
 * This array would be an associative array with a standard set of keys for your system that don't change.
 */
$paymentDetails = [];

/**
 * If you created purchase/refund/etc. methods in your gateway classes then you can call it like this.
 */
$response = $gateway->purchase($paymentDetails);

/**
 * If you did not create those methods then you can call the purchase like this. Obviously the one above is cleaner here
 * but it makes the classes more complex so that is personal choice. In our system the one above works better.
 */
$response = $gateway->gateway->purchase($gateway->getPurchaseData($paymentDetails))->send();

/**
 * Get the transaction ID and code like so
 */
$transactionId = $gateway->getTransactionId($response);
$transactionCode = $gateway->getTransactionResultCode($response);

/**
 * Refunds are basically run the same as the purchase
 */