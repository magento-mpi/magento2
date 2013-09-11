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
namespace Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Price;

class Recurring
    extends \Magento\Adminhtml\Block\Catalog\Form\Renderer\Fieldset\Element
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
        \Mage::dispatchEvent('catalog_product_edit_form_render_recurring', array(
            'result' => $result,
            'product_element' => $this->_element,
            'product'   => \Mage::registry('current_product'),
        ));
        return $result->output;
    }
}
