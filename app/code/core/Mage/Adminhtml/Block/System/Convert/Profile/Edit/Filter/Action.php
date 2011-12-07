<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * System Convert History action filter
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Convert_Profile_Edit_Filter_Action
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Abstract
{
    public function getHtml()
    {
        $values = array(
            ''       => '',
            'create' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Create'),
            'run'    => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Run'),
            'update' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Update'),
        );
        $value = $this->getValue();

        $html  = '<select name="' . ($this->getColumn()->getName() ? $this->getColumn()->getName() : $this->getColumn()->getId()) . '" ' . $this->getColumn()->getValidateClass() . '>';
        foreach ($values as $k => $v) {
            $html .= '<option value="'.$k.'"' . ($value == $k ? ' selected="selected"' : '') . '>'.$v.'</option>';
        }
        $html .= '</select>';
        return $html;
    }
}
