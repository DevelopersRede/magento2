<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Rede\Adquirencia\Model\Adapter;

use Rede\Adquirencia\Gateway\Config\Config;
use Magento\Framework\ObjectManagerInterface;

/**
 * This factory is preferable to use for Rede adapter instance creation.
 */
class RedeAdapterFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param Config $config
     */
    public function __construct(ObjectManagerInterface $objectManager, Config $config)
    {
        $this->config = $config;
        $this->objectManager = $objectManager;
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function create($storeId = null)
    {
        return $this->objectManager->create(
            RedeAdapter::class,
            [
                'config' => $this->config
            ]
        );
    }
}
