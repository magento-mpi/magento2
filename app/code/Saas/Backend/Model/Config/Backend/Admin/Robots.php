<?php
/**
 * Config backend model for robots.txt
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Backend_Model_Config_Backend_Admin_Robots extends Mage_Backend_Model_Config_Backend_Admin_Robots
{
    /**
     * @param Mage_Core_Model_Context $context
     * @param Magento_Filesystem $filesystem
     * @param Mage_Core_Model_Dir $directoryModel
     * @param Mage_Core_Model_Resource_Abstract $resource
     * @param Varien_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_Context $context,
        Magento_Filesystem $filesystem,
        Mage_Core_Model_Dir $directoryModel,
        Mage_Core_Model_Resource_Abstract $resource = null,
        Varien_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $filesystem, $resource, $resourceCollection, $data);
        $this->_filePath = $directoryModel->getDir('media') . '/' . 'robots.txt';
    }
}
