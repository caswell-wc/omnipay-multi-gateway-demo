<?php

namespace gateways;


use Omnipay\Common\CreditCard;
use Omnipay\Common\Message\ResponseInterface;

abstract class AbstractGateway implements GatewayInterface
{
    /** @var \Omnipay\Common\AbstractGateway */
    public $gateway;

    protected $pathToSecurityKeys = '<path to security key files on your server>';

    /**
     * Get the data needed for a purchase
     *
     * @param array $paymentDetails The full data from the original json sent to be processed.
     */
    protected abstract function getPurchaseData($paymentDetails);

    /**
     * Get the data needed for a refund
     *
     * @param array $paymentDetails The full data from the original json sent to be processed.
     *
     * @return mixed
     */
    protected abstract function getRefundData($paymentDetails);

    /**
     * Get the data needed for a void
     *
     * @param array $paymentDetails The full data from the original json sent to be processed.
     *
     * @return mixed
     */
    protected abstract function getVoidData($paymentDetails);

    /**
     * Get the transaction ID from the response. This is the default interaction that most of the gateways will use but
     * it can be overriden in the specific gateway's class such as AuthorizeNet.
     *
     * @param \Omnipay\Common\Message\ResponseInterface $response
     *
     * @return null|string
     */
    public function getTransactionId(ResponseInterface $response)
    {
        return $response->getTransactionReference();
    }

    /**
     * Get the transaction result code from the response. This is the default interaction that most of the gateways will
     * use but it can be overriden in the specific gateway's class such as AuthorizeNet.
     *
     * @param \Omnipay\Common\Message\ResponseInterface $response
     *
     * @return null|string
     */
    public function getTransactionResultCode(ResponseInterface $response)
    {
        return $response->getCode();
    }

    /**
     * Run a purchase transaction. If the gateway needs specific actions such as separating the auth and capture you can
     * do that by overriding this function.
     *
     * This and the refund method are not necessarily needed but they do create a
     * convenient way to run purchases or refunds in different ways depending on the gateway.
     *
     * @param array $paymentDetails The full data from the original json to be processed.
     *
     * @return \Omnipay\Common\Message\ResponseInterface
     */
    public function purchase($paymentDetails)
    {
        $message = $this->gateway->purchase($this->getPurchaseData($paymentDetails, $transactionKey));
        return $message->send();
    }

    /**
     * Run a refund transaction. By default this will run a void if the refund does not come back as successful. If the
     * gateway needs to run the refund differently, you can do that by overriding this function.
     *
     * This and the purchase method are not necessarily needed but they do create a
     * convenient way to run purchases or refunds in different ways depending on the gateway.
     *
     * @param array $paymentDetails
     *
     * @return \Omnipay\Common\Message\ResponseInterface
     */
    public function refund($paymentDetails)
    {
        $message = $this->gateway->refund($this->getRefundData($paymentDetails, $transactionKey));
        return $message->send();
    }

    /**
     * This is being used to create an Omnipay CreditCard object. This is needed so we can override the function if a
     * gateway driver has its own CreditCard object.
     *
     * @param $transaction
     *
     * @return \Omnipay\Common\CreditCard
     */
    public function generateCardObj($transaction)
    {
        return new CreditCard($transaction);
    }
}