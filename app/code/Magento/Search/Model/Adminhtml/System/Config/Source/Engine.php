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

    /**
     * @var Magento_Search_Helper_Data
     */
    protected $_helper = null;

    /**
     * Default constructor
     * @param array $arguments
     */
    public function __construct(array $arguments = array())
    {
        if (isset($arguments['helper'])) {
            $this->_helper = $arguments['helper'];
        }
    }

    /**
     * Get helper
     * @return Magento_Search_Helper_Data|null
     */
    protected function _getHelper()
    {
        if (is_null($this->_helper)) {
            $this->_helper = Mage::helper('Magento_Search_Helper_Data');
        }
        return $this->_helper;
    }

    public function toOptionArray()
    {
        $engines = array(
            self::FULLTEXT => $this->_getHelper()->__('MySql Fulltext'),
            self::SOLR => $this->_getHelper()->__('Solr')
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
