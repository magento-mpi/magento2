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
    protected $_typeList;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Cache_TypeListInterface $typeList
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Cache_TypeListInterface $typeList,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_typeList = $typeList;
        parent::__construct($context, $resource, $resourceCollection, $data);
    }

    /**
     * Invalidate cache type, when value was changed
     *
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            $this->_typeList->invalidate(Magento_Core_Block_Abstract::CACHE_GROUP);
        }
    }
}
