<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Db;

class Updater implements \Magento\Core\Model\Db\UpdaterInterface
{
    /**
     * Default setup class name
     *
     * @var string
     */
    protected $_defaultClass = 'Magento\Core\Model\Resource\Setup';

    /**
     * Setup model factory
     *
     * @var \Magento\Core\Model\Resource\SetupFactory
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
     * @var \Magento\Core\Model\App\State
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
     * @var \Magento\Core\Model\ModuleListInterface
     */
    protected $_moduleList;

    /**
     * @var \Magento\Core\Model\Module\ResourceResolverInterface
     */
    protected $_resourceResolver;

    /**
     * @param \Magento\Core\Model\Resource\SetupFactory $factory
     * @param \Magento\Core\Model\App\State $appState
     * @param \Magento\Core\Model\ModuleListInterface $moduleList
     * @param \Magento\Core\Model\Module\ResourceResolverInterface $resourceResolver
     * @param array $resourceList
     * @param bool $skipModuleUpdate
     */
    public function __construct(
        \Magento\Core\Model\Resource\SetupFactory $factory,
        \Magento\Core\Model\App\State $appState,
        \Magento\Core\Model\ModuleListInterface $moduleList,
        \Magento\Core\Model\Module\ResourceResolverInterface $resourceResolver,
        array $resourceList,
        $skipModuleUpdate = false
    ) {
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

        \Magento\Profiler::start('apply_db_schema_updates');
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

        /** @var $setupClass \Magento\Core\Model\Resource\SetupInterface*/
        foreach ($afterApplyUpdates as $setupClass) {
            $setupClass->afterApplyAllUpdates();
        }

        $this->_appState->setUpdateMode(false);
        $this->_isUpdatedSchema = true;
        \Magento\Profiler::stop('apply_db_schema_updates');
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
