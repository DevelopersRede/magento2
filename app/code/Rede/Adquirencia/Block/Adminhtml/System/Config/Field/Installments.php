<?php
namespace Rede\Adquirencia\Block\Adminhtml\System\Config\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\BlockInterface;

class Installments extends AbstractFieldArray
{

    /**
     * @var Installment
     */
    protected $_installmentRenderer = null;

    /**
     * Prepare to render
     * @return void
     * @throws LocalizedException
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'amount',
            [
                'label' => __('Amount Range (minor units)'),
                'renderer' => false,
            ]
        );
        $this->addColumn(
            'installments',
            [
                'label' => __('Max Number Of Installments'),
                'renderer' => $this->getNumberOfInstallmentsRenderer(),
            ]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Rule');
    }

    /**
     * Return renderer for installments
     * @return Installment|BlockInterface
     * @throws LocalizedException
     */
    protected function getNumberOfInstallmentsRenderer()
    {
        if (!$this->_installmentRenderer) {
            $this->_installmentRenderer = $this->getLayout()->createBlock(
                '\Rede\Adquirencia\Block\Adminhtml\System\Config\Field\Installment',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_installmentRenderer;
    }
}
