<?php
/**
 * Magento 2 EnvironmentAction.php
 * @author neto
 * @since 25/09/18
 */

namespace Rede\Adquirencia\Model\Adminhtml\Source;



class Installments implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return array_map(
            function ($i) {
                return [
                    'value' => $i,
                    'label' => sprintf('%dx', $i)
                ];
            },
            range(1, 12)
        );
    }

}