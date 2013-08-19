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
 * Adminhtml product edit tabs
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tabs extends Magento_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
    protected $_attributeTabBlock = 'Magento_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes';

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->addTab('bundle_items', array(
            'label'     => __('Bundle Items'),
            'url'   => $this->getUrl('*/*/bundles', array('_current' => true)),
            'class' => 'ajax',
        ));
        $this->bindShadowTabs('bundle_items', 'customer_options');
    }
}
