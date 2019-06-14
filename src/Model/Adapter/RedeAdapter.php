<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Rede\Adquirencia\Model\Adapter;

use Magento\Payment\Model\Method\Logger;
use Rede\Adquirencia\Gateway\Config\Config;
use Rede\Adquirencia\Model\Adminhtml\Source\Environment;

/**
 * Class RedeAdapter
 * @codeCoverageIgnore
 */
class RedeAdapter
{

    /**
     * @var Config
     */
    private $config;

    private $logger;

    /**
     * @param Config $config
     * @param Logger $logger
     */
    public function __construct(Config $config, Logger $logger = null)
    {
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @param array $attributes
     * @param bool $capture
     *
     * @return \Rede\Transaction
     * @throws \Exception
     */
    public function authorize(array $attributes, $capture = false)
    {
        $payment = $attributes['Sale']['Payment'];
        $pv = $this->config->getPv();
        $token = $this->config->getToken();
        $environment = \Rede\Environment::production();
        $softDescriptor = $this->config->getSoftDescriptor();
        $module = $this->config->getModule();
        $gateway = $this->config->getGateway();

        if ($this->config->getEnvironment() == 'test') {
            $environment = \Rede\Environment::sandbox();
        }

        $store = new \Rede\Store($pv, $token, $environment);
        $expiration = [];
        $expirationMonth = '';
        $expirationYear = '';

        if (preg_match('/(\d{2})\/(\d{4})/', $payment['CreditCard']['ExpirationDate'], $expiration)) {
            $expirationMonth = $expiration[1];
            $expirationYear = $expiration[2];
        }

        $transaction = new \Rede\Transaction($payment['Amount'], $attributes['Sale']['orderId'] + time());

        if ($this->config->isDebitEnabled() && $payment['Type'] == 'DebitCard') {
            $transaction->debitCard(
                $payment['CreditCard']['CardNumber'],
                $payment['CreditCard']['SecurityCode'],
                $expirationMonth,
                $expirationYear,
                $payment['CreditCard']['Holder']
            );

            $transaction->threeDSecure(\Rede\ThreeDSecure::DECLINE_ON_FAILURE);

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');

            $baseurl = $storeManager->getStore()->getBaseUrl();

            $transaction->addUrl($baseurl . 'checkout/onepage/success/', \Rede\Url::THREE_D_SECURE_SUCCESS);
            $transaction->addUrl($baseurl . 'checkout/onepage/success/', \Rede\Url::THREE_D_SECURE_FAILURE);
        } else {
            $transaction->creditCard(
                $payment['CreditCard']['CardNumber'],
                $payment['CreditCard']['SecurityCode'],
                $expirationMonth,
                $expirationYear,
                $payment['CreditCard']['Holder']
            )->setInstallments(isset($payment['Installments']) ? $payment['Installments'] : 1);

            $transaction->capture($capture);

            if ($this->config->is3DSEnabled()) {
                if ($payment['Authenticate'] && $payment['Amount'] > $this->config->getThresholdAmount()) {
                    $transaction->threeDSecure(\Rede\ThreeDSecure::DECLINE_ON_FAILURE);

                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');

                    $baseurl = $storeManager->getStore()->getBaseUrl();

                    $transaction->addUrl($baseurl . 'checkout/onepage/success/', \Rede\Url::THREE_D_SECURE_SUCCESS);
                    $transaction->addUrl($baseurl . 'checkout/onepage/success/', \Rede\Url::THREE_D_SECURE_FAILURE);
                }
            }
        }

        if (!empty($softDescriptor)) {
            $transaction->setSoftDescriptor($softDescriptor);
        }

        if (!empty($module) && !empty($gateway)) {
            $transaction->additional($gateway, $module);
        }

        $logger = new \Monolog\Logger('rede');
        $logger->pushHandler(new \Monolog\Handler\StreamHandler(BP . '/var/log/rede.log', \Monolog\Logger::DEBUG));
        $logger->info('Log Rede');

        try {
            $transaction = (new \Rede\eRede($store, $logger))->create($transaction);
        } catch (\Exception $e) {
            $logger->error($e->getMessage());
        }

        return $transaction;
    }

    public function capture(array $data)
    {
        $pv = $this->config->getPv();
        $token = $this->config->getToken();
        $environment = \Rede\Environment::production();

        if ($this->config->getEnvironment() == 'test') {
            $environment = \Rede\Environment::sandbox();
        }

        $store = new \Rede\Store($pv, $token, $environment);

        $logger = new \Monolog\Logger('rede');
        $logger->pushHandler(new \Monolog\Handler\StreamHandler(BP . '/var/log/rede.log', \Monolog\Logger::DEBUG));
        $logger->info('Log Rede');
        $transaction = null;

        try {
            $transaction = (new \Rede\eRede($store, $logger))->capture((new \Rede\Transaction($data['AMOUNT']))->setTid($data['TID']));
        } catch (\Exception $e) {
            $logger->error($e->getMessage());
        }

        return $transaction;
    }

    public function void(array $data)
    {
        $pv = $this->config->getPv();
        $token = $this->config->getToken();
        $environment = \Rede\Environment::production();

        if ($this->config->getEnvironment() == 'test') {
            $environment = \Rede\Environment::sandbox();
        }

        $store = new \Rede\Store($pv, $token, $environment);

        $logger = new \Monolog\Logger('rede');
        $logger->pushHandler(new \Monolog\Handler\StreamHandler(BP . '/var/log/rede.log', \Monolog\Logger::DEBUG));
        $logger->info('Log Rede');
        $transaction = null;

        try {
            $transaction = (new \Rede\eRede($store, $logger))->cancel((new \Rede\Transaction($data['AMOUNT']))->setTid($data['TID']));
        } catch (\Exception $e) {
            $logger->error($e->getMessage());
        }

        return $transaction;
    }

    public function getEnvironment()
    {
        $result = null;
        if ($this->config->getEnvironment() == Environment::ENVIRONMENT_TEST) {
            $result = Environment::sandbox();
        }

        return $result;
    }

    public function getMerchant()
    {
        return new Merchant($this->config->getPv(), $this->config->getToken());
    }

}
