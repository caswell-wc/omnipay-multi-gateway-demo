<?php

namespace gateways;

use Omnipay\AuthorizeNet\AIMGateway;
use Omnipay\AuthorizeNet\Message\AIMResponse;
use Omnipay\AuthorizeNet\Model\TransactionReference;
use Omnipay\Common\CreditCard;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Omnipay;

class AuthorizeNet extends AbstractGateway
{

    /** @var AIMGateway */
    public $gateway;
    /** @var array List of required credentials for Authorize.net */
    public $requiredCredentials = ['GatewayUsername', 'BankNumber'];

    /**
     * AuthorizeNet constructor.
     *
     * This instantiates the gateway and stores it in the gateway property of this object. Notice that setApiLoginId and
     * setTransactionKey are specific functions to the Authorize.net driver.
     *
     * @param $credentials
     */
    public function __construct($credentials)
    {
        $this->gateway = Omnipay::create('AuthorizeNet_AIM');
        $this->gateway->setApiLoginId($credentials->GatewayUsername);
        $this->gateway->setTransactionKey($credentials->BankNumber);
    }

    /**
     * Here is where we are grabbing transaction data differently because of the different way that the Auth.net driver
     * returns the transaction reference.
     *
     * @param AIMResponse $response
     *
     * @return mixed
     */
    public function getTransactionId(ResponseInterface $response)
    {
        $transactionReference = $response->getTransactionReference();
        if(!empty($transactionReference)){
            $transactionReference = json_decode($response->getTransactionReference());
            return $transactionReference->transId;
        }
        return '';
    }

    /**
     * Here is where we are grabbing transaction data differently because of the different way that the Auth.net driver
     * returns the transaction reference.
     *
     * @param AIMResponse $response
     *
     * @return mixed
     */
    public function getTransactionResultCode(ResponseInterface $response)
    {
        $transactionReference = $response->getTransactionReference();
        if(!empty($transactionReference)) {
            $transactionReference = json_decode($response->getTransactionReference());
            return $transactionReference->approvalCode;
        }
        return '';
    }

    /**
     * Get the formatted data needed for performing a refund to Authorize.net. Notice that the TransactionReference object
     * is being recreated manually. This is because the data is not stored as a json in our system.
     *
     * @param array $paymentDetails
     *
     * @return array
     */
    protected function getRefundData($paymentDetails)
    {
        $transaction = $paymentDetails['transactions'][$transactionKey];
        $referenceData = [
            'transId'=>$transaction['transactionId'],
            'card'=>[
                'number'=>$transaction['number'],
                'expiry'=>$transaction['expiryMonth'] . $transaction['expiryYear']
            ]
        ];

        $transactionReference = new TransactionReference(json_encode($referenceData));
        return [
            'transactionReference'=>$transactionReference,
            'amount'=>$transaction['amount'],
            'voidIfRefundFails'=>true
        ];
    }

    /**
     * Get the formatted data needed for performing a purchase to Authorize.net
     *
     * @param array $paymentDetails
     *
     * @return array
     */
    protected function getPurchaseData($paymentDetails)
    {
        $transaction = $paymentDetails['transactions'][$transactionKey];
        return [
            'card'=>$this->generateCardObj($transaction),
            'currency'=>$transaction['currency'],
            'amount'=>$transaction['amount']
        ];
    }

    /**
     * The data for a void should be the same as the data for a refund so this is simply calling that function and returning
     * it.
     *
     * @param array $paymentDetails
     *
     * @return array
     */
    protected function getVoidData($paymentDetails)
    {
        return $this->getRefundData($paymentDetails, $transactionKey);
    }
}