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
 * Url rewrite suffix backend
 */
class Magento_Catalog_Model_System_Config_Backend_Catalog_Url_Rewrite_Suffix extends Magento_Core_Model_Config_Value
{
    /**
     * Core url rewrite
     *
     * @var Magento_Core_Helper_Url_Rewrite
     */
    protected $_coreUrlRewrite = null;

    /**
     * @param Magento_Core_Helper_Url_Rewrite $coreUrlRewrite
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Url_Rewrite $coreUrlRewrite,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_coreUrlRewrite = $coreUrlRewrite;
        parent::__construct($context, $resource, $resourceCollection, $data);
    }

    /**
     * Check url rewrite suffix - whether we can support it
     *
     * @return Magento_Catalog_Model_System_Config_Backend_Catalog_Url_Rewrite_Suffix
     */
    protected function _beforeSave()
    {
        $this->_coreUrlRewrite->validateSuffix($this->getValue());
        return $this;
    }
}
