<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml Catalog Product List Sortable allowed sortable attributes source
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Source_Catalog_ListSort
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
            'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Best Value'),
            'value' => 'position'
        );
        foreach ($this->_getCatalogConfig()->getAttributesUsedForSortBy() as $attribute) {
            $options[] = array(
                'label' => Mage::helper('Mage_Catalog_Helper_Data')->__($attribute['frontend_label']),
                'value' => $attribute['attribute_code']
            );
        }
        return $options;
    }

    /**
     * Retrieve Catalog Config Singleton
     *
     * @return Mage_Catalog_Model_Config
     */
    protected function _getCatalogConfig() {
        return Mage::getSingleton('Mage_Catalog_Model_Config');
    }
}
