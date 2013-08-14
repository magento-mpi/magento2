<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Bundle Special Price Attribute Block
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes_Special extends Magento_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element
{
    public function getElementHtml()
    {
        $html = '<input id="'.$this->getElement()->getHtmlId().'" name="'.$this->getElement()->getName()
             .'" value="'.$this->getElement()->getEscapedValue().'" '.$this->getElement()->serialize($this->getElement()->getHtmlAttributes()).'/>'."\n"
             .'<label class="addafter" for="' . $this->getElement()->getHtmlId() . '"><strong>[%]</strong></label>';
        return $html;
    }
}
