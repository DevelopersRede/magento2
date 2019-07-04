<?php
namespace Rede\Adquirencia\Block\Adminhtml\System\Config\Field;

class Installment extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * Options
     *
     * @var array
     */
    protected $_options = [ '1' => '1x',
                            '2' => '2x',
                            '3' => '3x',
                            '4' => '4x',
                            '5' => '5x',
                            '6' => '6x',
                            '7' => '7x',
                            '8' => '8x',
                            '9' => '9x',
                            '10' => '10x',
                            '11' => '11x',
                            '12' => '12x'
    ];

    /**
     * Sets name for input element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
}
