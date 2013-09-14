<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomAttribute
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * EAV Entity Attribute Form Renderer Block for File
 *
 * @category    Magento
 * @package     Magento_CustomAttribute
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CustomAttribute_Block_Form_Renderer_File extends Magento_CustomAttribute_Block_Form_Renderer_Abstract
{
    /**
     * Return escaped value
     *
     * @return string
     */
    public function getEscapedValue()
    {
        if ($this->getValue()) {
            return $this->escapeHtml($this->_coreData->urlEncode($this->getValue()));
        }
        return '';
    }
}
