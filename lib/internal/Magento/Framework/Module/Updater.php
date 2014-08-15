<?php
/**
 * Application module updater. Used to install/upgrade module schemas.
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
     * Flag which allow run data install/upgrade
     *
     * @var bool
     */
    protected $_isUpdatedSchema = false;

    /**
     * Application state model
     *
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

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
     * @param State $appState
     * @param ModuleListInterface $moduleList
     * @param ResourceResolverInterface $resourceResolver
     * @param Manager $moduleManager
     */
    public function __construct(
        Updater\SetupFactory $setupFactory,
        State $appState,
        ModuleListInterface $moduleList,
        ResourceResolverInterface $resourceResolver,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->_appState = $appState;
        $this->_moduleList = $moduleList;
        $this->_resourceResolver = $resourceResolver;
        $this->_setupFactory = $setupFactory;
        $this->_moduleManager = $moduleManager;
    }

    /**
     * Apply database scheme updates whenever needed
     *
     * @return void
     */
    public function updateScheme()
    {
        \Magento\Framework\Profiler::start('apply_db_schema_updates');
        $this->_appState->setUpdateMode(true);

        $afterApplyUpdates = array();
        foreach (array_keys($this->_moduleList->getModules()) as $moduleName) {
            foreach ($this->_resourceResolver->getResourceList($moduleName) as $resourceName) {
                if (!$this->_moduleManager->isDbSchemaUpToDate($moduleName, $resourceName)) {
                    $setup = $this->_setupFactory->create($resourceName, $moduleName);
                    $setup->applyUpdates();

                    if ($setup->getCallAfterApplyAllUpdates()) {
                        $afterApplyUpdates[] = $setup;
                    }
                }
            }
        }

        /** @var $setup \Magento\Framework\Module\Updater\SetupInterface*/
        foreach ($afterApplyUpdates as $setup) {
            $setup->afterApplyAllUpdates();
        }

        $this->_appState->setUpdateMode(false);
        $this->_isUpdatedSchema = true;
        \Magento\Framework\Profiler::stop('apply_db_schema_updates');
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
