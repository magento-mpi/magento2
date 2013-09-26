<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Private sales setup model resource
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\WebsiteRestriction\Model\Resource;

class Setup extends \Magento\Core\Model\Resource\Setup
{
    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_pageFactory;

    /**
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\Config\Resource $resourcesConfig
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Core\Model\ModuleListInterface $moduleList
     * @param \Magento\Core\Model\Resource $resource
     * @param \Magento\Core\Model\Config\Modules\Reader $modulesReader
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param $resourceName
     */
    public function __construct(
        \Magento\Core\Model\Logger $logger,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\Config\Resource $resourcesConfig,
        \Magento\Core\Model\Config $config,
        \Magento\Core\Model\ModuleListInterface $moduleList,
        \Magento\Core\Model\Resource $resource,
        \Magento\Core\Model\Config\Modules\Reader $modulesReader,
        \Magento\Cms\Model\PageFactory $pageFactory,
        $resourceName
    ) {
        $this->_pageFactory = $pageFactory;
        parent::__construct(
            $logger, $eventManager, $resourcesConfig, $config, $moduleList, $resource, $modulesReader, $resourceName
        );
    }

    /**
     * @return \Magento\Cms\Model\Page
     */
    public function getPage()
    {
        return $this->_pageFactory->create();
    }
}
