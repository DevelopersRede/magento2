<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Rede\Adquirencia\Gateway\Http;

use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Gateway\ConfigInterface;



class TransferFactory implements TransferFactoryInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var TransferBuilder
     */
    private $transferBuilder;

    /**
     * @param TransferBuilder $transferBuilder
     * @param ConfigInterface $config
     */
    public function __construct(
        TransferBuilder $transferBuilder,
        ConfigInterface $config
    ) {

        $this->transferBuilder = $transferBuilder;
        $this->config = $config;
    }

    /**
     * Builds gateway transfer object
     *
     * @param array $request
     * @return TransferInterface
     */
    public function create(array $request)
    {
      return $this->transferBuilder
          ->setBody($request)
          ->build();
    }
}
