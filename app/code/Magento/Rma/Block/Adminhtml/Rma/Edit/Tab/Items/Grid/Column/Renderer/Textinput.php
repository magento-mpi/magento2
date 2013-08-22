<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Grid column widget for rendering cells, which can be of text or select type
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Textinput
    extends Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Abstract
{
    /**
     * Renders quantity as integer
     *
     * @param Magento_Object $row
     * @return int|string
     */
    public function _getValue(Magento_Object $row)
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
     * @param   Magento_Object $row
     * @return  string
     */
    protected function _getEditableView(Magento_Object $row)
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
