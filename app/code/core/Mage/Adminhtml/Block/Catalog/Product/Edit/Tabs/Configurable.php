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
 * admin edit tabs for configurable products
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs_Configurable extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
    protected function _prepareLayout()
    {
//        $product = $this->getProduct();

//        if (!($superAttributes = $product->getTypeInstance()->getUsedProductAttributeIds())) {
            $this->addTab('super_settings', array(
                'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Configurable Product Settings'),
                'content'   => $this->getLayout()
                    ->createBlock('Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Settings')
                    ->toHtml(),
                'active'    => true
            ));

//        } else {
//            parent::_prepareLayout();
//
//            $this->addTab('configurable', array(
//                'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Associated Products'),
//                'content'   => $this->getLayout()
//                     ->createBlock(
//                         'Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config',
//                         'admin.super.config.product'
//                     )->setProductId($this->getRequest()->getParam('id'))
//                    ->toHtml(),
//            ));
//            $this->bindShadowTabs('configurable', 'customer_options');
//        }
    }
}
