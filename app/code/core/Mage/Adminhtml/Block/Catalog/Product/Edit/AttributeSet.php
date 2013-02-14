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
 * Create product attribute set selector
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_AttributeSet extends Mage_Backend_Block_Widget_Form
{

    /**
     * Get options for suggest widget
     *
     * @return array
     */
    public function getSelectorOptions()
    {
        return array(
            'source' => $this->getUrl('*/catalog_product/suggestProductTemplates'),
            'className' => 'category-select',
            'showRecent' => true,
            'storageKey' => 'product-template-key',
            'minLength' => 0,
            'ajaxData' => array('current_template_id' => Mage::registry('product')->getAttributeSetId()),
        );
    }
}
