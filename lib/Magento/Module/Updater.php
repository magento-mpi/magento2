<?php
/**
 * Application module updater. Used to install/upgrade module schemas.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Module;

use Magento\App\State;
use Magento\Module\Updater\SetupFactory;
use Magento\Module\ModuleListInterface;
use Magento\Module\ResourceResolverInterface;

class Updater implements \Magento\Module\UpdaterInterface
{
    /**
     * Setup model factory
     *
     * @var SetupFactory
     */
    protected $_factory;

    /**
     * Flag which allow run data install/upgrade
     *
     * @var bool
     */
    protected $_isUpdatedSchema = false;

    /**
     * Application state model
     *
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * if it set to true, we will ignore applying scheme updates
     *
     * @var bool
     */
    protected $_skipModuleUpdate;

    /**
     * Map that contains setup model names per resource name
     *
     * @var array
     */
    protected $_resourceList;

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
     * @param Updater\SetupFactory $setupFactory
     * @param State $appState
     * @param ModuleListInterface $moduleList
     * @param ResourceResolverInterface $resourceResolver
     * @param bool $skipModuleUpdate
     */
    public function __construct(
        Updater\SetupFactory $setupFactory,
        State $appState,
        ModuleListInterface $moduleList,
        ResourceResolverInterface $resourceResolver,
        $skipModuleUpdate = false
    ) {
        $this->_appState = $appState;
        $this->_moduleList = $moduleList;
        $this->_resourceResolver = $resourceResolver;
        $this->_setupFactory = $setupFactory;
        $this->_skipModuleUpdate = $skipModuleUpdate;
    }

    /**
     * Check whether modules updates processing should be skipped
     *
     * @return bool
     */
    protected function _shouldSkipProcessModulesUpdates()
    {
        if (!$this->_appState->isInstalled()) {
            return false;
        }

        return $this->_skipModuleUpdate;
    }

    /**
     * Apply database scheme updates whenever needed
     *
     * @return void
     */
    public function updateScheme()
    {
        if ($this->_shouldSkipProcessModulesUpdates()) {
            return;
        }

        \Magento\Profiler::start('apply_db_schema_updates');
        $this->_appState->setUpdateMode(true);

        $afterApplyUpdates = array();
        foreach (array_keys($this->_moduleList->getModules()) as $moduleName) {
            foreach ($this->_resourceResolver->getResourceList($moduleName) as $resourceName) {
                $setup = $this->_setupFactory->create($resourceName, $moduleName);
                $setup->applyUpdates();

                if ($setup->getCallAfterApplyAllUpdates()) {
                    $afterApplyUpdates[] = $setup;
                }
            }
        }

        /** @var $setup \Magento\Module\Updater\SetupInterface*/
        foreach ($afterApplyUpdates as $setup) {
            $setup->afterApplyAllUpdates();
        }

        $this->_appState->setUpdateMode(false);
        $this->_isUpdatedSchema = true;
        \Magento\Profiler::stop('apply_db_schema_updates');
    }

    /**
     * Apply database data updates whenever needed
     *
     * @return void
     */
    public function updateData()
    {
        if (!$this->_isUpdatedSchema) {
            return;
        }
        foreach (array_keys($this->_moduleList->getModules()) as $moduleName) {
            foreach ($this->_resourceResolver->getResourceList($moduleName) as $resourceName) {
                $this->_setupFactory->create($resourceName, $moduleName)->applyDataUpdates();
            }
        }
    }
}
