<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Rede\Adquirencia\Model\Adapter;

use Exception;
use Magento\Framework\App\ObjectManager;
use Magento\Payment\Model\Method\Logger;
use Monolog\Handler\StreamHandler;
use Rede\Adquirencia\Gateway\Config\Config;
use Rede\Adquirencia\Model\Adminhtml\Source\Environment;
use Rede\Device;
use Rede\eRede;
use Rede\Store;
use Rede\Transaction;
use Rede\Url;

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
     * @return Transaction
     * @throws Exception
     */
    public function authorize(array $attributes, $capture = false)
    {
        $payment = $attributes['Sale']['Payment'];
        $pv = $this->config->getPv();
        $token = $this->config->getToken();
        $environment = $this->config->getEnvironment() === 'test' ? \Rede\Environment::sandbox() : \Rede\Environment::production();
        $softDescriptor = $this->config->getSoftDescriptor();
        $store = new Store($pv, $token, $environment);
        $expiration = [];
        $expirationMonth = '';
        $expirationYear = '';

        if (preg_match('/(\d{2})\/(\d{4})/', $payment['CreditCard']['ExpirationDate'], $expiration)) {
            $expirationMonth = $expiration[1];
            $expirationYear = $expiration[2];
        }

        $transaction = new Transaction($payment['Amount'], $attributes['Sale']['orderId'] + time());
        $debit = false;

        if ($this->config->isDebitEnabled() && $payment['Type'] == 'debit') {
            $debit = true;
            $transaction->debitCard(
                $payment['CreditCard']['CardNumber'],
                $payment['CreditCard']['SecurityCode'],
                $expirationMonth,
                $expirationYear,
                $payment['CreditCard']['Holder']
            );
        } else {
            $transaction->creditCard(
                $payment['CreditCard']['CardNumber'],
                $payment['CreditCard']['SecurityCode'],
                $expirationMonth,
                $expirationYear,
                $payment['CreditCard']['Holder']
            )->setInstallments($payment['Installments'] ?? 1);

            $transaction->capture($capture);
        }

        $logger = new \Monolog\Logger('rede');
        $logger->pushHandler(new StreamHandler(BP . '/var/log/rede.log', \Monolog\Logger::DEBUG));
        $logger->info('Log Rede');

        if ($debit || $this->config->is3DSEnabled()) {
            if ($debit || $payment['Amount'] > $this->config->getThresholdAmount()) {
                $transaction->threeDSecure(
                    new Device(
                        ColorDepth: $payment['Device']['color_depth'],
                        DeviceType3ds: 'BROWSER',
                        JavaEnabled: true,
                        Language: 'BR',
                        ScreenHeight: $payment['Device']['screen_height'],
                        ScreenWidth: $payment['Device']['screen_width']
                    )
                );

                $objectManager = ObjectManager::getInstance();
                $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');

                $baseurl = $storeManager->getStore()->getBaseUrl();

                $transaction->addUrl($baseurl . 'checkout/onepage/success/', Url::THREE_D_SECURE_SUCCESS);
                $transaction->addUrl($baseurl . 'checkout/onepage/success/', Url::THREE_D_SECURE_FAILURE);
            }
        }

        if (!empty($softDescriptor)) {
            $transaction->setSoftDescriptor($softDescriptor);
        }

        try {
            $transaction = (new eRede($store, $logger))->create($transaction);
        } catch (Exception $e) {
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

        $store = new Store($pv, $token, $environment);

        $logger = new \Monolog\Logger('rede');
        $logger->pushHandler(new StreamHandler(BP . '/var/log/rede.log', \Monolog\Logger::DEBUG));
        $logger->info('Log Rede');
        $transaction = null;

        try {
            $transaction = (new eRede(
                $store,
                $logger
            ))->capture((new Transaction($data['AMOUNT']))->setTid($data['TID']));
        } catch (Exception $e) {
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

        $store = new Store($pv, $token, $environment);

        $logger = new \Monolog\Logger('rede');
        $logger->pushHandler(new StreamHandler(BP . '/var/log/rede.log', \Monolog\Logger::DEBUG));
        $logger->info('Log Rede');
        $transaction = null;

        try {
            $transaction = (new eRede(
                $store,
                $logger
            ))->cancel((new Transaction($data['AMOUNT']))->setTid($data['TID']));
        } catch (Exception $e) {
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
