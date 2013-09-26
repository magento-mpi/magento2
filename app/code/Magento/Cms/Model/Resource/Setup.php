<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cms resource setup
 */
namespace Magento\Cms\Model\Resource;

class Setup extends \Magento\Core\Model\Resource\Setup\Generic
{
    /**
     * Block factory
     *
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $_blockFactory;

    /**
     * Page factory
     *
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_pageFactory;

    /**
     * Construct
     *
     * @param \Magento\Core\Model\Resource\Setup\MigrationFactory $migrationFactory
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\Config\Resource $resourcesConfig
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Core\Model\ModuleListInterface $moduleList
     * @param \Magento\Core\Model\Resource $resource
     * @param \Magento\Core\Model\Config\Modules\Reader $modulesReader
     * @param $resourceName
     * @param \Magento\Cms\Model\BlockFactory $blockFactory
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     */
    public function __construct(
        \Magento\Core\Model\Resource\Setup\MigrationFactory $migrationFactory,
        \Magento\Core\Model\Logger $logger,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\Config\Resource $resourcesConfig,
        \Magento\Core\Model\Config $config,
        \Magento\Core\Model\ModuleListInterface $moduleList,
        \Magento\Core\Model\Resource $resource,
        \Magento\Core\Model\Config\Modules\Reader $modulesReader,
        $resourceName,
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \Magento\Cms\Model\PageFactory $pageFactory
    ) {
        parent::__construct($migrationFactory, $logger, $eventManager, $resourcesConfig, $config, $moduleList,
            $resource, $modulesReader, $resourceName);

        $this->_blockFactory = $blockFactory;
        $this->_pageFactory = $pageFactory;
    }

    /**
     * Create block
     *
     * @return \Magento\Cms\Model\Block
     */
    public function createBlock()
    {
        return $this->_blockFactory->create();
    }

    /**
     * Create page
     *
     * @return \Magento\Cms\Model\Page
     */
    public function createPage()
    {
        return $this->_pageFactory->create();
    }
}
