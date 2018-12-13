<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Rede\Adquirencia\Gateway\Request;

/**
 * Payment Data Builder
 */
class CreditCardPaymentDataBuilder extends AbstractPaymentDataBuilder
{
    /**
     * retorna o tipo de transação
     * @param array $buildSubject
     * @return string
     */
    public function getTypeTransaction(array $buildSubject=[])
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);
        $payment = $paymentDO->getPayment();
        $debit = false;

        if ($payment->getAdditionalInformation('debitCard')) {
            $debit = $payment->getAdditionalInformation('debitCard');
        }

        return $debit? self::PAYMENTTYPE_DEBITCARD : self::PAYMENTTYPE_CREDITCARD;
    }

    /**
     * retorna o tipo de transação
     * @param array $buildSubject
     * @return bool|mixed
     */
    public function is3DS(array $buildSubject=[])
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);
        $payment = $paymentDO->getPayment();
        $auth = false;

        if ($payment->getAdditionalInformation('creditCard3Ds')) {
            $auth = $payment->getAdditionalInformation('creditCard3Ds');
        }

        return $auth;
    }

}
