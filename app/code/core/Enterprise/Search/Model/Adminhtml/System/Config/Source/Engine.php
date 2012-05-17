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
    const FULLTEXT = 'mysql_fulltext';
    const SOLR = 'solr';

    public function toOptionArray()
    {
        $engines = array(
            self::FULLTEXT => Mage::helper('Enterprise_Search_Helper_Data')
                ->__('MySql Fulltext'),
            self::SOLR => Mage::helper('Enterprise_Search_Helper_Data')
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
