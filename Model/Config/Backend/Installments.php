<?php

namespace Rede\Adquirencia\Model\Config\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class InstallmentsValues extends Value
{
    /**
     * @var Random
     */
    protected $mathRandom;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param Random $mathRandom
     * @param AbstractResource $resource
     * @param AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        Random $mathRandom,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        $this->mathRandom = $mathRandom;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * Prepare data before save
     * @return $this
     */
    public function beforeSave()
    {
        $value = $this->getValue();

        $result = [];
        foreach ($value as $data) {
            if (!$data) {
                continue;
            }
            if (!is_array($data)) {
                continue;
            }
            if (count($data) < 2) {
                continue;
            }

            $amount = $data['amount'];
            $installments = $data['installments'];
            $ccTypes = $data['cc_types'];

            foreach ($ccTypes as $ccType) {
                $result[$ccType][$amount] = $installments;
            }
        }

        $this->setValue(serialize($result));
        return $this;
    }

    /**
     * Process data after load
     * @return $this
     * @throws LocalizedException
     */
    protected function _afterLoad()
    {
        $value = $this->getValue();
        $value = unserialize($value);
        if (is_array($value)) {
            $value = $this->encodeArrayFieldValue($value);
            $this->setValue($value);
        }
        return $this;
    }

    /**
     * Encode value to be used in \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param array $value
     *
     * @return array
     * @throws LocalizedException
     */
    protected function encodeArrayFieldValue(array $value)
    {
        $result = [];

        // first combine the ccTypes together
        $list = [];
        foreach ($value as $ccType => $items) {

            // sort on amount
            ksort($items);

            foreach ($items as $amount => $installment) {
                if (!isset($list[$installment][$amount])) {
                    $list[$installment][$amount] = [$ccType];
                } else {
                    $ccTypes = $list[$installment][$amount];
                    $ccTypes[] = $ccType;
                    $list[$installment][$amount] = $ccTypes;
                }
            }
        }

        // loop through combined ccTypes configuration and pre fill the items
        foreach ($list as $installment => $amounts) {
            foreach ($amounts as $amount => $ccTypes) {
                $resultId = $this->mathRandom->getUniqueHash('_');
                $result[$resultId] = ['amount' => $amount, 'cc_types' => $ccTypes, 'installments' => $installment];
            }
        }

        return $result;
    }
}
