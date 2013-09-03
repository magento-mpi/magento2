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
 * Grid column widget for rendering action grid cells
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Textselect
    extends Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Abstract
{
    /**
     * Renders column as select when it is editable
     *
     * @param   \Magento\Object $row
     * @return  string
     */
    protected function _getEditableView(\Magento\Object $row)
    {
        $selectName = 'items[' . $row->getId() . '][' . $this->getColumn()->getId() . ']';
        $html = '<select name="'. $selectName .'" class="action-select required-entry">';
        $value = $row->getData($this->getColumn()->getIndex());
        $html.= '<option value=""></option>';
        foreach ($this->getColumn()->getOptions() as $val => $label){
            $selected = ( ($val == $value && (!is_null($value))) ? ' selected="selected"' : '' );
            $html.= '<option value="' . $val . '"' . $selected . '>' . $label . '</option>';
        }
        $html.='</select>';
        return $html;
    }

}
