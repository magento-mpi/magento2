<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * EAV Entity Attribute Form Renderer Block for File
 *
 * @category    Enterprise
 * @package     Enterprise_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Eav_Block_Form_Renderer_File extends Enterprise_Eav_Block_Form_Renderer_Abstract
{
    /**
     * Return escaped value
     *
     * @return string
     */
    public function getEscapedValue()
    {
        if ($this->getValue()) {
            return $this->escapeHtml(Mage::helper('Magento_Core_Helper_Data')->urlEncode($this->getValue()));
        }
        return '';
    }
}
