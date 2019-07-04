<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Rede\Adquirencia\Gateway\Http\Client;

/**
 * Class TransactionSale
 */
class TransactionSale extends AbstractTransaction
{
    /**
     * @inheritdoc
     * @throws \Exception
     */
    protected function process(array $data)
    {
        return $this->adapter->authorize($data, true);
    }
}
