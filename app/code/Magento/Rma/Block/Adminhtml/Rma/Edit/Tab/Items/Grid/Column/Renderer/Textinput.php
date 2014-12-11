<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Grid column widget for rendering cells, which can be of text or select type
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\Items\Grid\Column\Renderer;

class Textinput extends \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\Items\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Renders quantity as integer
     *
     * @param \Magento\Framework\Object $row
     * @return int|string
     */
    public function _getValue(\Magento\Framework\Object $row)
    {
        $quantity = parent::_getValue($row);
        if (!$row->getIsQtyDecimal()) {
            $quantity = intval($quantity);
        }
        return $quantity;
    }

    /**
     * Renders column as input when it is editable
     *
     * @param   \Magento\Framework\Object $row
     * @return  string
     */
    protected function _getEditableView(\Magento\Framework\Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        if (!$row->getIsQtyDecimal() && !is_null($value)) {
            $value = intval($value);
        }
        $class = 'input-text ' . $this->getColumn()->getValidateClass();
        $html = '<input type="text" ';
        $html .= 'name="items[' . $row->getId() . '][' . $this->getColumn()->getId() . ']" ';
        $html .= 'value="' . $value . '" ';
        if ($this->getStatusManager()->getAttributeIsDisabled($this->getColumn()->getId())) {
            $html .= ' disabled="disabled" ';
            $class .= ' disabled ';
        }
        $html .= 'class="' . $class . '" />';
        return $html;
    }
}
