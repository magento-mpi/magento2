<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Backend_Model_Config_Backend_Storage_Media_Database extends Magento_Core_Model_Config_Value
{
    /**
     * Core file storage
     *
     * @var Magento_Core_Helper_File_Storage
     */
    protected $_coreFileStorage = null;

    /**
     * @param Magento_Core_Helper_File_Storage $coreFileStorage
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_File_Storage $coreFileStorage,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_coreFileStorage = $coreFileStorage;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Create db structure
     *
     * @return Magento_Backend_Model_Config_Backend_Storage_Media_Database
     */
    protected function _afterSave()
    {
        $helper = $this->_coreFileStorage;
        $helper->getStorageModel(null, array('init' => true));

        return $this;
    }
}
