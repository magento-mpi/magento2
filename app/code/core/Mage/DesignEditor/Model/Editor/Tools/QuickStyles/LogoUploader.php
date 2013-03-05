<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * System config Logo image field backend model
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_DesignEditor_Model_Editor_Tools_QuickStyles_LogoUploader extends Mage_Backend_Model_Config_Backend_Image_Logo
{
    /**
     * @param Mage_Core_Model_Event_Manager $eventDispatcher
     * @param Mage_Core_Model_CacheInterface $cacheManager
     * @param Mage_DesignEditor_Model_Config_Backend_File_RequestData $requestData
     * @param Magento_Filesystem $filesystem
     * @param Mage_Core_Model_Resource_Abstract $resource
     * @param Varien_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_Event_Manager $eventDispatcher,
        Mage_Core_Model_CacheInterface $cacheManager,
        Mage_DesignEditor_Model_Config_Backend_File_RequestData $requestData,
        Magento_Filesystem $filesystem,
        Mage_Core_Model_Resource_Abstract $resource = null,
        Varien_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct(
            $eventDispatcher, $cacheManager, $requestData, $filesystem, $resource, $resourceCollection, $data
        );
    }
}
