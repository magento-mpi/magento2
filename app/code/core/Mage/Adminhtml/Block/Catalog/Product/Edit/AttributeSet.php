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
     * Get encoded options for suggest widget
     *
     * @return string
     */
    public function getSelectorOptions()
    {
        return Mage::helper('Mage_Core_Helper_Data')->jsonEncode(array(
            'source' => Mage::helper('Mage_Backend_Helper_Data')
                ->getUrl('adminhtml/catalog_product/suggestAttributeSet'),
            'className' => 'category-select',
            'template' => '#product-template-selector-template',
            'showRecent' => true,
            'storageKey' => 'product-template-key',
            'minLength' => 0,
            'ajaxData' => array('current_template_id' => Mage::registry('product')->getAttributeSetId()),
        ));
    }
}
