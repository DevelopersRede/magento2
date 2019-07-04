<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Rede\Adquirencia\Block\Adminhtml\View;

use Magento\Sales\Model\Order;
use Rede\Adquirencia\Gateway\Config\Config;

/**
 * Class TransactionUpdate
 */
class TransactionUpdate
{
    private $config;

    /**
     * TransactionUpdate constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function beforeSetLayout(\Magento\Sales\Block\Adminhtml\Order\View $view)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        /**
         * @var \Magento\Sales\Model\Order
         */
        $order = $objectManager->create('\Magento\Sales\Model\Order')->load($view->getOrderId());
        $payment = $order->getPayment();
        $canConsult = empty($payment->getAdditionalInformation("Nsu")) && empty($payment->getAdditionalInformation("Id Refund")) && empty($payment->getAdditionalInformation("Id Cancel")) && $payment->getMethodInstance()->getCode() == 'rede';

        if ($canConsult) {
            $oldstatus = $payment->getAdditionalInformation("Status da Autorização");

            $environment = \Rede\Environment::production();

            if ($this->config->getEnvironment() == 'test') {
                $environment = \Rede\Environment::sandbox();
            }

            $store = new \Rede\Store($this->config->getPv(), $this->config->getToken(), $environment);

            $logger = new \Monolog\Logger('rede');
            $logger->pushHandler(new \Monolog\Handler\StreamHandler(BP . '/var/log/rede.log', \Monolog\Logger::DEBUG));
            $logger->info('Log Rede');

            $transaction = (new \Rede\eRede($store, $logger))->get($payment->getAdditionalInformation('Id Transação'));
            $status = $transaction->getAuthorization()->getStatus();

            if ($status != $oldstatus) {
                switch ($status) {
                    case 'Approved':
                        $order->setState(Order::STATE_PROCESSING);
                        break;
                    case 'Canceled':
                    case 'Denied':
                        $order->setState(Order::STATE_CANCELED);
                        break;
                    case 'Pending':
                        $order->setState(Order::STATE_PENDING_PAYMENT);
                        break;
                }

                $payment->setAdditionalInformation("Status da Autorização", $status);
                $order->addStatusHistoryComment(sprintf('Status updated to %s', $status));
                $order->save();
            }
        }
    }
}
