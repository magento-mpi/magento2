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
 * admin edit tabs for configurable products
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Edit_Tabs_Configurable extends Magento_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
    /**
     * Preparing layout
     *
     * @return Magento_Adminhtml_Block_Catalog_Product_Edit_Tabs_Configurable
     */
    protected function _prepareLayout()
    {
        $this->addTab('super_settings', array(
            'label'     => __('Configurable Product Settings'),
            'content'   => $this->getLayout()
                ->createBlock('Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Settings')
                ->toHtml(),
            'active'    => true
        ));
    }
}
