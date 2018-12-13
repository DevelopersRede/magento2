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
class SaleDataBuilder implements BuilderInterface
{

    const SALE = 'Sale';

    const ORDER_ID = 'orderId';

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

        $order = $paymentDO->getOrder();
        $result = [];

        $result[self::SALE] = [
          self::ORDER_ID => $order->getOrderIncrementId()
        ];

        return $result;
    }
}
