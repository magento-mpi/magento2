<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Db_Updater implements Magento_Core_Model_Db_UpdaterInterface
{
    /**
     * Modules configuration
     *
     * @var Magento_Core_Model_Config
     */
    protected $_config;

    /**
     * Default setup class name
     *
     * @var string
     */
    protected $_defaultClass = 'Magento_Core_Model_Resource_Setup';

    /**
     * Setup model factory
     *
     * @var Magento_Core_Model_Resource_SetupFactory
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
     * @var Magento_Core_Model_App_State
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
     * @var Magento_Core_Model_ModuleListInterface
     */
    protected $_moduleList;

    /**
     * @var Magento_Core_Model_Module_ResourceResolverInterface
     */
    protected $_resourceResolver;

    /**
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_Resource_SetupFactory $factory
     * @param Magento_Core_Model_App_State $appState
     * @param Magento_Core_Model_ModuleListInterface $moduleList
     * @param Magento_Core_Model_Module_ResourceResolverInterface $resourceResolver
     * @param array $resourceList
     * @param bool $skipModuleUpdate
     */
    public function __construct(
        Magento_Core_Model_Config $config,
        Magento_Core_Model_Resource_SetupFactory $factory,
        Magento_Core_Model_App_State $appState,
        Magento_Core_Model_ModuleListInterface $moduleList,
        Magento_Core_Model_Module_ResourceResolverInterface $resourceResolver,
        array $resourceList,
        $skipModuleUpdate = false
    ) {
        $this->_config = $config;
        $this->_factory = $factory;
        $this->_appState = $appState;
        $this->_moduleList = $moduleList;
        $this->_resourceResolver = $resourceResolver;
        $this->_resourceList = $resourceList;
        $this->_skipModuleUpdate = (bool)$skipModuleUpdate;
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
     */
    public function updateScheme()
    {
        if ($this->_shouldSkipProcessModulesUpdates()) {
            return;
        }

        Magento_Profiler::start('apply_db_schema_updates');
        $this->_appState->setUpdateMode(true);

        $afterApplyUpdates = array();
        foreach (array_keys($this->_moduleList->getModules()) as $moduleName) {
            foreach ($this->_resourceResolver->getResourceList($moduleName) as $resourceName) {
                $className = isset($this->_resourceList[$resourceName])
                    ? $this->_resourceList[$resourceName]
                    : $this->_defaultClass;

                $setupClass = $this->_factory->create(
                    $className,
                    array(
                        'resourceName' => $resourceName,
                        'moduleName' => $moduleName,
                    )
                );
                $setupClass->applyUpdates();

                if ($setupClass->getCallAfterApplyAllUpdates()) {
                    $afterApplyUpdates[] = $setupClass;
                }
            }
        }

        /** @var $setupClass Magento_Core_Model_Resource_SetupInterface*/
        foreach ($afterApplyUpdates as $setupClass) {
            $setupClass->afterApplyAllUpdates();
        }

        $this->_appState->setUpdateMode(false);
        $this->_isUpdatedSchema = true;
        Magento_Profiler::stop('apply_db_schema_updates');
    }

    /**
     * Apply database data updates whenever needed
     */
    public function updateData()
    {
        if (!$this->_isUpdatedSchema) {
            return;
        }
        foreach (array_keys($this->_moduleList->getModules()) as $moduleName) {
            foreach ($this->_resourceResolver->getResourceList($moduleName) as $resourceName) {
                $className = isset($this->_resourceList[$resourceName])
                    ? $this->_resourceList[$resourceName]
                    : $this->_defaultClass;
                $setupClass = $this->_factory->create($className, array('resourceName' => $resourceName,
                    'moduleName' => $moduleName,));
                $setupClass->applyDataUpdates();
            }
        }
    }
}
