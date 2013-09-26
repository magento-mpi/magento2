<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Event resource setup
 */
namespace Magento\CatalogEvent\Model\Resource;

class Setup extends \Magento\Sales\Model\Resource\Setup
{
    /**
     * Block model factory
     *
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $_blockFactory;

    /**
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\Config\Resource $resourcesConfig
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Core\Model\ModuleListInterface $moduleList
     * @param \Magento\Core\Model\Resource $resource
     * @param \Magento\Core\Model\Config\Modules\Reader $modulesReader
     * @param \Magento\Core\Model\CacheInterface $cache
     * @param \Magento\Core\Model\Resource\Setup\MigrationFactory $migrationFactory
     * @param \Magento\Cms\Model\BlockFactory $modelBlockFactory
     * @param string $resourceName
     */
    public function __construct(
        \Magento\Core\Model\Logger $logger,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\Config\Resource $resourcesConfig,
        \Magento\Core\Model\Config $config,
        \Magento\Core\Model\ModuleListInterface $moduleList,
        \Magento\Core\Model\Resource $resource,
        \Magento\Core\Model\Config\Modules\Reader $modulesReader,
        \Magento\Core\Model\CacheInterface $cache,
        \Magento\Core\Model\Resource\Setup\MigrationFactory $migrationFactory,
        \Magento\Cms\Model\BlockFactory $modelBlockFactory,
        $resourceName
    ) {
        $this->_blockFactory = $modelBlockFactory;
        parent::__construct($logger, $coreData, $eventManager, $resourcesConfig, $config, $moduleList,
            $resource, $modulesReader, $cache, $migrationFactory, $resourceName);
    }

    /**
     * Get model block factory
     *
     * @return \Magento\Cms\Model\BlockFactory
     */
    public function getBlockFactory()
    {
        return $this->_blockFactory;
    }
}
