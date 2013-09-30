<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Resource\Setup;

class Context
{
    /**
     * @var \Magento\Core\Model\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\Core\Model\Event\Manager
     */
    protected $_eventManager;

    /**
     * @var \Magento\Core\Model\Resource
     */
    protected $_resourceModel;

    /**
     * @var \Magento\Core\Model\Config\Modules\Reader
     */
    protected $_modulesReader;

    /**
     * @var \Magento\Core\Model\ModuleListInterface
     */
    protected $_moduleList;

    /**
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\Resource $resource
     * @param \Magento\Core\Model\Config\Modules\Reader $modulesReader
     * @param \Magento\Core\Model\ModuleListInterface $moduleList
     */
    public function __construct(
        \Magento\Core\Model\Logger $logger,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\Resource $resource,
        \Magento\Core\Model\Config\Modules\Reader $modulesReader,
        \Magento\Core\Model\ModuleListInterface $moduleList
    ) {
        $this->_logger = $logger;
        $this->_eventManager = $eventManager;
        $this->_resourceModel = $resource;
        $this->_modulesReader = $modulesReader;
        $this->_moduleList = $moduleList;
    }

    /**
     * @return \Magento\Core\Model\Event\Manager
     */
    public function getEventManager()
    {
        return $this->_eventManager;
    }

    /**
     * @return \Magento\Core\Model\Logger
     */
    public function getLogger()
    {
        return $this->_logger;
    }

    /**
     * @return \Magento\Core\Model\ModuleListInterface
     */
    public function getModuleList()
    {
        return $this->_moduleList;
    }

    /**
     * @return \Magento\Core\Model\Config\Modules\Reader
     */
    public function getModulesReader()
    {
        return $this->_modulesReader;
    }

    /**
     * @return \Magento\Core\Model\Resource
     */
    public function getResourceModel()
    {
        return $this->_resourceModel;
    }
}
