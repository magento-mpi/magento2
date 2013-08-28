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
 * Enterprise attribute edit block
 *
 * @category   Enterprise
 * @package    Enterprise_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Block_Adminhtml_Catalog_Attribute_Edit extends Magento_Backend_Block_Template
{
    /**
     * Search data
     *
     * @var Enterprise_Search_Helper_Data
     */
    protected $_searchData = null;

    /**
     * @param Enterprise_Search_Helper_Data $searchData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Enterprise_Search_Helper_Data $searchData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_searchData = $searchData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Return true if third part search engine used
     *
     * @return boolean
     */
    public function isThirdPartSearchEngine()
    {
        return $this->_searchData->isThirdPartSearchEngine();
    }
}
