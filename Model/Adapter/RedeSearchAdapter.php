<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Rede\Adquirencia\Model\Adapter;

/**
 * Class Rede Search Adapter
 */
class RedeSearchAdapter
{
    /**
     * @return TextNode
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public function id()
    {
        return TransactionSearch::id();
    }

    /**
     * @return MultipleValueNode
     */
    public function merchantAccountId()
    {
        return TransactionSearch::merchantAccountId();
    }

    /**
     * @return TextNode
     */
    public function orderId()
    {
        return TransactionSearch::orderId();
    }

    /**
     * @return TextNode
     */
    public function paypalPaymentId()
    {
        return TransactionSearch::paypalPaymentId();
    }

    /**
     * @return MultipleValueNode
     */
    public function createdUsing()
    {
        return TransactionSearch::createdUsing();
    }

    /**
     * @return MultipleValueNode
     */
    public function type()
    {
        return TransactionSearch::type();
    }

    /**
     * @return RangeNode
     */
    public function createdAt()
    {
        return TransactionSearch::createdAt();
    }

    /**
     * @return RangeNode
     */
    public function amount()
    {
        return TransactionSearch::amount();
    }

    /**
     * @return MultipleValueNode
     */
    public function status()
    {
        return TransactionSearch::status();
    }

    /**
     * @return TextNode
     */
    public function settlementBatchId()
    {
        return TransactionSearch::settlementBatchId();
    }

    /**
     * @return MultipleValueNode
     */
    public function paymentInstrumentType()
    {
        return TransactionSearch::paymentInstrumentType();
    }
}
