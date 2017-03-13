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
     * Run a purchase transaction.
     *
     * @param array $paymentDetails The full data from the original json to be processed.
     *
     * @return \Omnipay\Common\Message\ResponseInterface
     */
    public function purchase($paymentDetails);

    /**
     * Run a refund transaction.
     *
     * @param array $paymentDetails The full data from the original json to be processed.
     *
     * @return \Omnipay\Common\Message\ResponseInterface
     */
    public function refund($paymentDetails);

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