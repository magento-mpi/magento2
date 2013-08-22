<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog search types
 *
 * @category   Magento
 * @package    Magento_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogSearch_Model_Config_Source_Search_Type implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        $types = array(
            Magento_CatalogSearch_Model_Fulltext::SEARCH_TYPE_LIKE     => 'Like',
            Magento_CatalogSearch_Model_Fulltext::SEARCH_TYPE_FULLTEXT => 'Fulltext',
            Magento_CatalogSearch_Model_Fulltext::SEARCH_TYPE_COMBINE  => 'Combine (Like and Fulltext)',
        );
        $options = array();
        foreach ($types as $k => $v) {
            $options[] = array(
                'value' => $k,
                'label' => $v
            );
        }
        return $options;
    }
}
