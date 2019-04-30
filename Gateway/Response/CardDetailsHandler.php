<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Rede\Adquirencia\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Rede\Adquirencia\Gateway\Config\Config;
use Rede\Adquirencia\Gateway\Helper\SubjectReader;
use Rede\Transaction;

/**
 * Class CardDetailsHandler
 */
class CardDetailsHandler implements HandlerInterface
{
    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var Config
     */
    private $config;

    /**
     * Constructor
     *
     * @param SubjectReader $subjectReader
     * @param Config $config
     */
    public function __construct(SubjectReader $subjectReader, Config $config)
    {
        $this->subjectReader = $subjectReader;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = $this->subjectReader->readPayment($handlingSubject);
        $payment = $paymentDO->getPayment();

        /**
         * @var Transaction
         */
        $response_obj = $this->subjectReader->readTransaction($response);

        $payment->setCcLast4(substr($response_obj->getCardNumber(), -4));
        $payment->setCcExpMonth($response_obj->getExpirationMonth());
        $payment->setCcExpYear($response_obj->getExpirationYear());
        $payment->setCcType($response_obj->getCardBin());

        // set card details to additional info
        $payment->setAdditionalInformation('Titular do Cartão', $response_obj->getCardHolderName());
        $payment->setAdditionalInformation('Número do Cartão', $response_obj->getCardNumber());
        $payment->setAdditionalInformation('Expiração do cartão',
            sprintf('%02d/%04d', $response_obj->getExpirationMonth(), $response_obj->getExpirationYear()));
        $payment->setAdditionalInformation('Ambiente', $this->config->getEnvironment());
    }

}

