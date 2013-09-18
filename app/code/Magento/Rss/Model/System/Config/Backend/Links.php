<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cache cleaner backend model
 *
 */
class Magento_Rss_Model_System_Config_Backend_Links extends Magento_Core_Model_Config_Value
{
    /**
     * @var Magento_Core_Model_Cache_TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Cache_TypeListInterface $cacheTypeList
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Cache_TypeListInterface $cacheTypeList,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_cacheTypeList = $cacheTypeList;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Invalidate cache type, when value was changed
     *
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            $this->_cacheTypeList->invalidate(Magento_Core_Block_Abstract::CACHE_GROUP);
        }
    }

}
