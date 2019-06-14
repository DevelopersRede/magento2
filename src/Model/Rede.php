<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Rede\Adquirencia\Model;

/**
 * Class ConfigProvider
 */
class Rede extends \Magento\Payment\Model\Method\Cc
{
    const METHOD_CODE = 'rede';

    protected $_code = self::METHOD_CODE;
    protected $_isGateway = true;
    protected $_canCapture= true;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = true;

}
