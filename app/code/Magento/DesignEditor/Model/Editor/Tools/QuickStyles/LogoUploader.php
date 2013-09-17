<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * System config Logo image field backend model
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Magento_DesignEditor_Model_Editor_Tools_QuickStyles_LogoUploader
    extends Magento_Backend_Model_Config_Backend_Image_Logo
{
    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_DesignEditor_Model_Config_Backend_File_RequestData $requestData
     * @param Magento_Filesystem $filesystem
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_DesignEditor_Model_Config_Backend_File_RequestData $requestData,
        Magento_Filesystem $filesystem,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct(
            $context, $registry, $requestData, $filesystem, $resource, $resourceCollection, $data
        );
    }
}
