<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Product List Sortable allowed sortable attributes source
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Config_Source_ListSort implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Retrieve option values array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        $options[] = array(
            'label' => __('Position'),
            'value' => 'position'
        );
        foreach ($this->_getCatalogConfig()->getAttributesUsedForSortBy() as $attribute) {
            $options[] = array(
                'label' => __($attribute['frontend_label']),
                'value' => $attribute['attribute_code']
            );
        }
        return $options;
    }

    /**
     * Retrieve Catalog Config Singleton
     *
     * @return Magento_Catalog_Model_Config
     */
    protected function _getCatalogConfig() {
        return Mage::getSingleton('Magento_Catalog_Model_Config');
    }
}
