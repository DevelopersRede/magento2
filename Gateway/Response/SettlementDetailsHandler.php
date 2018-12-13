<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Rede\Adquirencia\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Rede\Adquirencia\Gateway\Helper\SubjectReader;

class SettlementDetailsHandler implements HandlerInterface
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

        if (!is_null($response_obj)) {
            $payment = $paymentDO->getPayment();

            $payment->setAdditionalInformation("Codigo de Retorno", $response_obj->getReturnCode());
            $payment->setAdditionalInformation("Messagem de Retorno", $response_obj->getReturnMessage());

            $payment->setAdditionalInformation("Nsu", $response_obj->getNsu());
            $payment->setAdditionalInformation("Código da autorização", $response_obj->getAuthorizationCode());

            $authorization = $response_obj->getAuthorization();

            if (!is_null($authorization)) {
                $payment->setAdditionalInformation("Status da autorização", $authorization->getStatus());
            }

            /** @var $payment \Magento\Sales\Model\Order\Payment */
            //$payment->setTransactionId($response[self::TXN_ID]);
            $payment->setTransactionId($response_obj->getTid());
            $payment->setParentTransactionId($payment->getTransactionId());
            $payment->setIsTransactionClosed(false)->setTransactionAdditionalInfo('Reponse', $response_obj->jsonSerialize());
        }
    }
}
