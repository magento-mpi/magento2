<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog search types
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Model_Adminhtml_System_Config_Source_Engine
{
    public function toOptionArray()
    {
        $engines = array(
            'Mage_CatalogSearch_Model_Resource_Fulltext_Engine' => Mage::helper('Enterprise_Search_Helper_Data')
                ->__('MySql Fulltext'),
            'Enterprise_Search_Model_Resource_Engine' => Mage::helper('Enterprise_Search_Helper_Data')
                ->__('Solr')
        );
        $options = array();
        foreach ($engines as $k => $v) {
            $options[] = array(
                'value' => $k,
                'label' => $v
            );
        }
        return $options;
    }
}
