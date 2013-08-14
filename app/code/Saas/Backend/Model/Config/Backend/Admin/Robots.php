<?php
/**
 * Config backend model for robots.txt
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Backend_Model_Config_Backend_Admin_Robots extends Magento_Backend_Model_Config_Backend_Admin_Robots
{
    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Filesystem $filesystem
     * @param Magento_Core_Model_Dir $directoryModel
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Filesystem $filesystem,
        Magento_Core_Model_Dir $directoryModel,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $filesystem, $resource, $resourceCollection, $data);
        $this->_filePath = $directoryModel->getDir('media') . '/' . 'robots.txt';
    }
}
