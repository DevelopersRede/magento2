<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Rede\Adquirencia\Block\Adminhtml\View;

use Magento\Framework\App\ObjectManager;
use Magento\Sales\Block\Adminhtml\Order\View;
use Magento\Sales\Model\Order;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Rede\Adquirencia\Gateway\Config\Config;
use Rede\Environment;
use Rede\eRede;
use Rede\Store;

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

    public function beforeSetLayout(View $view)
    {
        $objectManager = ObjectManager::getInstance();

        /**
         * @var Order
         */
        $order = $objectManager->create('\Magento\Sales\Model\Order')->load($view->getOrderId());
        $payment = $order->getPayment();
        $canConsult = empty($payment->getAdditionalInformation('Nsu')) && empty($payment->getAdditionalInformation('Id Refund')) && empty($payment->getAdditionalInformation('Id Cancel')) && $payment->getMethodInstance()->getCode() == 'rede';

        if ($canConsult) {
            $oldstatus = $payment->getAdditionalInformation('Status da Autorização');

            $environment = Environment::production();

            if ($this->config->getEnvironment() == 'test') {
                $environment = Environment::sandbox();
            }

            $store = new Store($this->config->getPv(), $this->config->getToken(), $environment);

            $logger = new Logger('rede');
            $logger->pushHandler(new StreamHandler(BP . '/var/log/rede.log', Logger::DEBUG));
            $logger->info('Log Rede');

            $transaction = (new eRede($store, $logger))->get($payment->getAdditionalInformation('Id Transação'));
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

                $payment->setAdditionalInformation('Status da Autorização', $status);
                $order->addStatusHistoryComment(sprintf('Status updated to %s', $status));
                $order->save();
            }
        }
    }
}
