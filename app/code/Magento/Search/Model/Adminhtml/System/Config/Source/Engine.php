<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog search types
 *
 * @category    Magento
 * @package     Magento_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Search_Model_Adminhtml_System_Config_Source_Engine
{
    const FULLTEXT = 'Magento_CatalogSearch_Model_Resource_Fulltext_Engine';
    const SOLR = 'Magento_Search_Model_Resource_Engine';

    public function toOptionArray()
    {
        $engines = array(
            self::FULLTEXT => __('MySql Fulltext'),
            self::SOLR => __('Solr')
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
