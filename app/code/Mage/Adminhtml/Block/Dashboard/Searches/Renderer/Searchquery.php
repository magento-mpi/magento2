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
 * Dashboard search query column renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Dashboard_Searches_Renderer_Searchquery
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Magento_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        if (Mage::helper('Mage_Core_Helper_String')->strlen($value) > 30) {
            $value = '<span title="'. $this->escapeHtml($value) .'">'
                . $this->escapeHtml(Mage::helper('Mage_Core_Helper_String')->truncate($value, 30)) . '</span>';
        }
        else {
            $value = $this->escapeHtml($value);
        }
        return $value;
    }
}
