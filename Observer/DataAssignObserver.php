<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Rede\Adquirencia\Observer;

use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;

class DataAssignObserver extends AbstractDataAssignObserver
{
    /**
     * @param Observer $observer
     *
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $data = $this->readDataArgument($observer);

        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
        if (!is_array($additionalData)) {
            return;
        }

        $additionalData = new DataObject($additionalData);
        $paymentMethod = $this->readMethodArgument($observer);

        $payment = $observer->getPaymentModel();
        
        if (!$payment instanceof InfoInterface) {
            $payment = $paymentMethod->getInfoInstance();
        }

        if (!$payment instanceof InfoInterface) {
            throw new LocalizedException(__('Payment model does not provided.'));
        }

        if ($additionalData->getData('number_of_installments')) {
            $payment->setAdditionalInformation('number_of_installments',
                $additionalData->getData('number_of_installments'));
        }

        if ($additionalData->getData('creditCard3Ds')) {
            $payment->setAdditionalInformation('creditCard3Ds',
                $additionalData->getData('creditCard3Ds'));
        }

        if ($additionalData->getData('debitCard')) {
            $payment->setAdditionalInformation('debitCard',
                $additionalData->getData('debitCard'));
        }

        $payment->setCcLast4(substr($additionalData->getData('cc_number'), -4));
        $payment->setCcNumberEnc($payment->encrypt($additionalData->getData('cc_number')));
        $payment->setCcNumber($additionalData->getData('cc_number'));
        $payment->setCcCid($additionalData->getData('cc_cid'));
        $payment->setCcType($additionalData->getData('cc_type'));
        $payment->setCcExpMonth($additionalData->getData('cc_exp_month'));
        $payment->setCcExpYear($additionalData->getData('cc_exp_year'));
        $payment->setCcOwner($additionalData->getData('cc_owner'));
    }
}
