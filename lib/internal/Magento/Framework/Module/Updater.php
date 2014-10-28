<?php
/**
 * Application module updater. Used to install/upgrade module data.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Module;

use Magento\Framework\App\State;

class Updater
{
    /**
     * @var ModuleListInterface
     */
    protected $_moduleList;

    /**
     * @var ResourceResolverInterface
     */
    protected $_resourceResolver;

    /**
     * @var Updater\SetupFactory
     */
    protected $_setupFactory;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $_moduleManager;

    /**
     * @param Updater\SetupFactory $setupFactory
     * @param ModuleListInterface $moduleList
     * @param ResourceResolverInterface $resourceResolver
     * @param Manager $moduleManager
     */
    public function __construct(
        Updater\SetupFactory $setupFactory,
        ModuleListInterface $moduleList,
        ResourceResolverInterface $resourceResolver,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->_moduleList = $moduleList;
        $this->_resourceResolver = $resourceResolver;
        $this->_setupFactory = $setupFactory;
        $this->_moduleManager = $moduleManager;
    }

    /**
     * Apply database data updates whenever needed
     *
     * @return void
     */
    public function updateData()
    {
        foreach (array_keys($this->_moduleList->getModules()) as $moduleName) {
            foreach ($this->_resourceResolver->getResourceList($moduleName) as $resourceName) {
                if (!$this->_moduleManager->isDbDataUpToDate($moduleName, $resourceName)) {
                    $this->_setupFactory->create($resourceName, $moduleName)->applyDataUpdates();
                }
            }
        }
    }
}
