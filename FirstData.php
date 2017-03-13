<?php

namespace gateways;


use Omnipay\Common\CreditCard;
use Omnipay\FirstData\WebserviceGateway;
use Omnipay\Omnipay;

class FirstData extends AbstractGateway
{

    /** @var  WebserviceGateway */
    public $gateway;
    /** @var string[] Credentials required for First Data */
    public $requiredCredentials = [];

    /**
     * FirstData constructor.
     *
     * First data uses a very different method for authentication and requires the use of key files that are stored on
     * the server. These kind of differences are one of the main reasons that we are using this kind of structure.
     *
     * @param $gateway
     *
     * @throws \Exception
     */
    public function __construct($gateway)
    {
        $pathToKeys = $this->pathToSecurityKeys;
        /**
         * You might do some logic here to determine which security keys on your server to use.
         */

        $credentials = json_decode(file_get_contents($pathToKeys . '/config.json'));
        /**
         * The config.json file contains key file names needed below.
         */
        $this->gateway = Omnipay::create('FirstData_Webservice');
        $this->gateway->initialize([
            'sslCertificate' => $pathToKeys . '/' . $credentials->certificateFile,
            'sslKey' => $pathToKeys . '/' . $credentials->keyFile,
            'sslKeyPassword' => $credentials->keyPassword,
            'username' => $credentials->username,
            'password' => $credentials->password
        ]);
        /**
         * Use some logic to determine how to set the test mode.
         */
        $this->gateway->setTestMode(true);
    }

    /**
     * Get the data needed for a purchase transaction. Notice the differences in what variables are returned for this
     * purchase as compared to the variables returned in the same function on the AuthorizeNet class.
     *
     * @param array $paymentDetails Full json sent to the api for being processed.
     *
     * @return array
     */
    protected function getPurchaseData($paymentDetails)
    {
        return [
            'accountId' => $paymentDetails['accountId'],
            'amount' => $paymentDetails['amount'],
            'transactionId' => uniqid(),
            'clientIp' => $paymentDetails['clientIp'],
            'card' => $this->generateCardObj($paymentDetails)
        ];
    }

    /**
     * Get the data necessary for a refund and format it. Notice the differences in what variables are returned for this
     * refund as compared to the variables returned in the same function on the AuthorizeNet class.
     *
     * @param array $paymentDetails Full json sent to the api for being processed.
     *
     * @return array
     */
    protected function getRefundData($paymentDetails)
    {
        //This if statement is here for backwards compatibility to the old way that transaction data was stored.
        if(strpos($paymentDetails['transactionId'], '::') !== false) {
            $transactionReference = $paymentDetails['transactionId'];
        } else {
            $transactionReference = $paymentDetails['transactionId'] . '::' . strtotime($paymentDetails['dateTime']);
        }

        return [
            'transactionReference' => $transactionReference,
            'amount' => $paymentDetails['amount']
        ];
    }

    /**
     * Get the data needed for a void transaction. With FirstData, the void data and refund data are the same so this is
     * just returning the result of getRefundData.
     *
     * @param array $paymentDetails Full json sent to the api for being processed.
     *
     * @return array
     */
    protected function getVoidData($paymentDetails)
    {
        return $this->getRefundData($paymentDetails);
    }
}