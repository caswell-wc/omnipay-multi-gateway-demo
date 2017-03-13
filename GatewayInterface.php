<?php

namespace gateways;


use Omnipay\Common\Message\ResponseInterface;

/**
 * Interface GatewayInterface
 *
 * This interface defines the functions that any gateway classes must contain.
 *
 * @package app\modules\internal\models\gateways
 *
 *
 */
interface GatewayInterface
{

    /**
     * Get the data for a purchase transaction.
     *
     * @param array $paymentDetails The full data from the original json to be processed.
     *
     * @return \Omnipay\Common\Message\ResponseInterface
     */
    public function getPurchaseData($paymentDetails);

    /**
     * Get the data for a refund transaction.
     *
     * @param array $paymentDetails The full data from the original json to be processed.
     *
     * @return \Omnipay\Common\Message\ResponseInterface
     */
    public function getRefundData($paymentDetails);

    /**
     * Get the data for a void transaction
     *
     * @param array $paymentDetails The full data from the original json to be processed.
     *
     * @return mixed
     */
    public function getVoidData($paymentDetails);

    /**
     * Get the transaction ID from the response
     *
     * @param \Omnipay\Common\Message\ResponseInterface $response
     *
     * @return null|string
     */
    public function getTransactionId(ResponseInterface $response);

    /**
     * Get the transaction result code from the response
     *
     * @param \Omnipay\Common\Message\ResponseInterface $response
     *
     * @return null|string
     */
    public function getTransactionResultCode(ResponseInterface $response);

}