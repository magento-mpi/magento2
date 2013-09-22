<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleShopping\Model\Resource;

class Setup extends \Magento\Core\Model\Resource\Setup
{
    /**
     * @var null
     */
    protected $_googleShoppingData = null;

    /**
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\GoogleShopping\Helper\Data $googleShoppingData
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\Config\Resource $resourcesConfig
     * @param \Magento\Core\Model\Config $modulesConfig
     * @param \Magento\Core\Model\ModuleListInterface $moduleList
     * @param \Magento\Core\Model\Resource $resource
     * @param \Magento\Core\Model\Config\Modules\Reader $modulesReader
     * @param $resourceName
     */
    public function __construct(
        \Magento\GoogleShopping\Helper\Data $googleShoppingData,
        \Magento\Core\Model\Logger $logger,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\Config\Resource $resourcesConfig,
        \Magento\Core\Model\Config $modulesConfig,
        \Magento\Core\Model\ModuleListInterface $moduleList,
        \Magento\Core\Model\Resource $resource,
        \Magento\Core\Model\Config\Modules\Reader $modulesReader,
        $resourceName
    ) {
        $this->_googleShoppingData = $googleShoppingData;
        parent::__construct(
            $logger, $eventManager, $resourcesConfig, $modulesConfig,
            $moduleList, $resource, $modulesReader, $resourceName
        );
    }

    /**
     * @return null
     */
    public function getGoogleShoppingData()
    {
        return $this->_googleShoppingData;
    }
}
