<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Directory Resource Setup Model
 *
 * @category    Magento
 * @package     Magento_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Directory\Model\Resource;

class Setup extends \Magento\Core\Model\Resource\Setup
{
    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $_directoryData;

    /**
     * @param \Magento\Directory\Helper\Data $directoryData
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\Config\Resource $resourcesConfig
     * @param \Magento\Core\Model\Config $modulesConfig
     * @param \Magento\Core\Model\ModuleListInterface $moduleList
     * @param \Magento\Core\Model\Resource $resource
     * @param \Magento\Core\Model\Config\Modules\Reader $modulesReader
     * @param $resourceName
     */
    public function __construct(
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\Config\Resource $resourcesConfig,
        \Magento\Core\Model\Config $modulesConfig,
        \Magento\Core\Model\ModuleListInterface $moduleList,
        \Magento\Core\Model\Resource $resource,
        \Magento\Core\Model\Config\Modules\Reader $modulesReader,
        $resourceName
    ) {
        parent::__construct(
            $eventManager, $resourcesConfig, $modulesConfig, $moduleList, $resource, $modulesReader, $resourceName
        );
        $this->_directoryData = $directoryData;
    }

    /**
     * @return \Magento\Directory\Helper\Data
     */
    public function getDirectoryData()
    {
        return $this->_directoryData;
    }
}
