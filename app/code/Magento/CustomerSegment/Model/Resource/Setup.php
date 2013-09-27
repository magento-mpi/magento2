<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * CustomerSegment resource setup
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CustomerSegment_Model_Resource_Setup extends Magento_Eav_Model_Entity_Setup
{
    /**
     * @var Magento_Enterprise_Model_Resource_Setup_MigrationFactory
     */
    protected $_migrationFactory;

    /**
     * @var Magento_CustomerSegment_Model_Resource_Segment_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Magento_Enterprise_Model_Resource_Setup_MigrationFactory $migrationFactory
     * @param Magento_CustomerSegment_Model_Resource_Segment_CollectionFactory $collectionFactory
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Config_Resource $resourcesConfig
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_ModuleListInterface $moduleList
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_Config_Modules_Reader $modulesReader
     * @param Magento_Core_Model_CacheInterface $cache
     * @param $resourceName
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Enterprise_Model_Resource_Setup_MigrationFactory $migrationFactory,
        Magento_CustomerSegment_Model_Resource_Segment_CollectionFactory $collectionFactory,
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Config_Resource $resourcesConfig,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_ModuleListInterface $moduleList,
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_Config_Modules_Reader $modulesReader,
        Magento_Core_Model_Resource_Resource $resourceResource,
        Magento_Core_Model_Resource_Theme_CollectionFactory $themeResourceFactory,
        Magento_Core_Model_Theme_CollectionFactory $themeFactory,
        Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory,
        Magento_Core_Model_CacheInterface $cache,
        Magento_Eav_Model_Resource_Entity_Attribute_Group_CollectionFactory $attrGrCollFactory,
        $resourceName
    ) {
        $this->_migrationFactory = $migrationFactory;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($logger, $eventManager, $resourcesConfig, $config, $moduleList, $resource, $modulesReader,
            $resourceResource, $themeResourceFactory, $themeFactory, $migrationFactory, $resourceName, $cache,
            $attrGrCollFactory);
    }

    /**
     * @param array $data
     * @return Magento_Enterprise_Model_Resource_Setup_Migration
     */
    public function createSetupMigration(array $data = array())
    {
        return $this->_migrationFactory->create($data);
    }

    /**
     * @return Magento_CustomerSegment_Model_Resource_Segment_Collection
     */
    public function createSegmentCollection()
    {
        return $this->_collectionFactory->create();
    }
}
