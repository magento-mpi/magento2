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
 * Recurring profile attribute edit renderer
 */
class Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Recurring
    extends Magento_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element
{
    /**
     * Element output getter
     *
     * @return string
     */
    public function getElementHtml()
    {
        $result = new StdClass;
        $result->output = '';
        Mage::dispatchEvent('catalog_product_edit_form_render_recurring', array(
            'result' => $result,
            'product_element' => $this->_element,
            'product'   => Mage::registry('current_product'),
        ));
        return $result->output;
    }
}
