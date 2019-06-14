<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Rede\Adquirencia\Gateway\Response;

use Rede\Adquirencia\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order;

class TransactionIdHandler implements HandlerInterface
{
    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * TransactionIdHandler constructor.
     * @param SubjectReader $subjectReader
     */
    public function __construct(
        SubjectReader $subjectReader
    ) {
        $this->subjectReader = $subjectReader;
    }

    /**
     * Handles response
     *
     * @param array $handlingSubject
     * @param array $response
     * @return void
     * @throws \Exception
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = $this->subjectReader->readPayment($handlingSubject);

        if ($paymentDO->getPayment() instanceof Payment) {
            /** @var \Rede\Transaction */
            $transaction = $this->subjectReader->readTransaction($response);

            /** @var Payment $orderPayment */
            $orderPayment = $paymentDO->getPayment();
            $this->setTransactionId(
                $orderPayment,
                $transaction
            );

            $orderPayment->setIsTransactionClosed($this->shouldCloseTransaction());
            $closed = $this->shouldCloseParentTransaction($orderPayment);
            $orderPayment->setShouldCloseParentTransaction($closed);

            $order = $orderPayment->getOrder();
            $order->setStatus(Order::STATUS);
            $order->setState(Order::STATE_CANCELED);
            $order->cancel();
            $order->save();
        }
    }

    /**
     * @param Payment $orderPayment
     * @param \Rede\Transaction $transaction
     * @return void
     */
    protected function setTransactionId(Payment $orderPayment, \Rede\Transaction $transaction)
    {
        $orderPayment->setTransactionId($transaction->getTid());
    }

    /**
     * Whether transaction should be closed
     *
     * @return bool
     */
    protected function shouldCloseTransaction()
    {
        return false;
    }

    /**
     * Whether parent transaction should be closed
     *
     * @param Payment $orderPayment
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function shouldCloseParentTransaction(Payment $orderPayment)
    {
        return false;
    }
}
