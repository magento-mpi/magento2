<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftMessage
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift Message resource setup
 */
namespace Magento\GiftMessage\Model\Resource;

class Setup extends \Magento\Sales\Model\Resource\Setup
{
    /**
     * @var \Magento\Catalog\Model\Resource\SetupFactory
     */
    protected $_catalogSetupFactory;

    /**
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\Config\Resource $resourcesConfig
     * @param \Magento\Core\Model\Config $modulesConfig
     * @param \Magento\Core\Model\ModuleListInterface $moduleList
     * @param \Magento\Core\Model\Resource $resource
     * @param \Magento\Core\Model\Config\Modules\Reader $modulesReader
     * @param \Magento\Core\Model\CacheInterface $cache
     * @param \Magento\Core\Model\Resource\Setup\MigrationFactory $migrationFactory
     * @param \Magento\Catalog\Model\Resource\SetupFactory $catalogSetupFactory
     * @param string $resourceName
     */
    public function __construct(
        \Magento\Core\Model\Logger $logger,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\Config\Resource $resourcesConfig,
        \Magento\Core\Model\Config $modulesConfig,
        \Magento\Core\Model\ModuleListInterface $moduleList,
        \Magento\Core\Model\Resource $resource,
        \Magento\Core\Model\Config\Modules\Reader $modulesReader,
        \Magento\Core\Model\CacheInterface $cache,
        \Magento\Core\Model\Resource\Setup\MigrationFactory $migrationFactory,
        \Magento\Catalog\Model\Resource\SetupFactory $catalogSetupFactory,
        $resourceName
    ) {
        $this->_catalogSetupFactory = $catalogSetupFactory;
        parent::__construct($logger, $coreData, $eventManager, $resourcesConfig, $modulesConfig, $moduleList,
            $resource, $modulesReader, $cache, $migrationFactory, $resourceName);
    }

    /**
     * Create Catalog Setup Factory for GiftMessage
     *
     * @param array $data
     * @return \Magento\Catalog\Model\Resource\Setup
     */
    public function createGiftMessageSetup(array $data = array())
    {
        return $this->_catalogSetupFactory->create($data);
    }
}
