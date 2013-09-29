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
namespace Magento\Catalog\Model\Config\Source;

class ListSort implements \Magento\Core\Model\Option\ArrayInterface
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
     * @return \Magento\Catalog\Model\Config
     */
    protected function _getCatalogConfig() {
        return \Mage::getSingleton('Magento\Catalog\Model\Config');
    }
}
