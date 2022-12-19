<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Rede\Adquirencia\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Rede\Adquirencia\Gateway\Helper\SubjectReader;

/**
 * Class AddressDataBuilder
 */
class CreditCardDataBuilder implements BuilderInterface
{
    /**
     * ShippingAddress block name
     */
    const CREDIT_CARD = 'CreditCard';
    const DEBIT_CARD = 'DebitCard';
    const CARDNUMBER = 'CardNumber';
    const HOLDER = 'Holder';
    const EXPIRATIONDATE = 'ExpirationDate';
    const SECURITYCODE = 'SecurityCode';

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
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);

        $payment = $paymentDO->getPayment();
        $result = [];
        $result[SaleDataBuilder::SALE] = [
            AbstractPaymentDataBuilder::PAYMENT => [
                self::CREDIT_CARD => [
                    self::CARDNUMBER => $payment->getCcNumber(),
                    self::HOLDER => $payment->getCcOwner(),
                    self::EXPIRATIONDATE => str_pad($payment->getCcExpMonth(), 2, '0',
                            STR_PAD_LEFT) . "/" . $payment->getCcExpYear(),
                    self::SECURITYCODE => $payment->getCcCid()
                ]
            ]
        ];

        return $result;
    }
}
