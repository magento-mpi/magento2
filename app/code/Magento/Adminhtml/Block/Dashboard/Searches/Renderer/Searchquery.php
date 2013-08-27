<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Dashboard search query column renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 */
class Magento_Adminhtml_Block_Dashboard_Searches_Renderer_Searchquery
    extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Magento_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        if (Mage::helper('Magento_Core_Helper_String')->strlen($value) > 30) {
            $value = '<span title="'. $this->escapeHtml($value) .'">'
                . $this->escapeHtml(Mage::helper('Magento_Core_Helper_String')->truncate($value, 30)) . '</span>';
        }
        else {
            $value = $this->escapeHtml($value);
        }
        return $value;
    }
}
