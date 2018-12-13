<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Rede\Adquirencia\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Rede\Adquirencia\Gateway\Helper\SubjectReader;
use Rede\Authorization;
use Rede\Transaction;

class PaymentDetailsHandler implements HandlerInterface
{

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * Constructor
     *
     * @param SubjectReader $subjectReader
     */
    public function __construct(SubjectReader $subjectReader)
    {
        $this->subjectReader = $subjectReader;
    }

    /**
     * Handles transaction id
     *
     * @param array $handlingSubject
     * @param array $response
     *
     * @return void
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = $this->subjectReader->readPayment($handlingSubject);

        /**
         * @var Transaction
         */
        $response_obj = $this->subjectReader->readTransaction($response);

        /**
         * @var Authorization
         */
        $authorization = $response_obj->getAuthorization();
        $capture = $response_obj->getCapture();

        $payment = $paymentDO->getPayment();

        if ($authorization !== null) {
            $payment->setCcTransId($response_obj->getAuthorization()->getTid());
            $payment->setLastTransId($response_obj->getAuthorization()->getTid());
        }

        $payment->setAdditionalInformation("Codigo de Retorno", $response_obj->getReturnCode());
        $payment->setAdditionalInformation("Messagem de Retorno", $response_obj->getReturnMessage());
        $payment->setAdditionalInformation("Parcelas", $response_obj->getInstallments());

        $payment->setAdditionalInformation("Id Transação", $response_obj->getTid());
        $payment->setAdditionalInformation("Id Refund", $response_obj->getRefundId());
        $payment->setAdditionalInformation("Id Cancel", $response_obj->getCancelId());
        $payment->setAdditionalInformation("Bin", $response_obj->getCardBin());
        $payment->setAdditionalInformation("Last 4", $response_obj->getLast4());
        $payment->setAdditionalInformation("Nsu", $response_obj->getNsu());
        $payment->setAdditionalInformation("Código da autorização", $response_obj->getAuthorizationCode());

        $payment->setTransactionId($response_obj->getTid());
        $payment->setParentTransactionId($payment->getTransactionId());
        $payment->setIsTransactionClosed(false)->setTransactionAdditionalInfo('Reponse', $response_obj->jsonSerialize());

        if (is_null($capture)) {
            $order = $payment->getOrder();
            $order->setStatus('Canceled');
            $order->setState(Order::STATE_CANCELED);
            $order->cancel();
            $order->save();
        }
    }
}
