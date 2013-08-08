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
    /**
     * Preparing layout
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs_Configurable
     */
    protected function _prepareLayout()
    {
        $this->addTab('super_settings', array(
            'label'     => __('Configurable Product Settings'),
            'content'   => $this->getLayout()
                ->createBlock('Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Settings')
                ->toHtml(),
            'active'    => true
        ));
    }
}
