<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Db_Updater implements Mage_Core_Model_Db_UpdaterInterface
{
    /**
     * Modules configuration
     *
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * Default setup class name
     *
     * @var string
     */
    protected $_defaultClass = 'Mage_Core_Model_Resource_Setup';

    /**
     * Setup model factory
     *
     * @var Mage_Core_Model_Resource_SetupFactory
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
     * @var Mage_Core_Model_App_State
     */
    protected $_appState;

    /**
     * @param Mage_Core_Model_Config $config
     * @param Mage_Core_Model_Resource_SetupFactory $factory
     * @param Mage_Core_Model_App_State $appState
     */
    public function __construct(
        Mage_Core_Model_Config $config,
        Mage_Core_Model_Resource_SetupFactory $factory,
        Mage_Core_Model_App_State $appState
    ) {
        $this->_config = $config;
        $this->_factory = $factory;
        $this->_appState = $appState;
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

        $ignoreDevMode = (bool)(string)$this->_config->getNode(self::XML_PATH_IGNORE_DEV_MODE);
        if (($this->_appState->getMode() == Mage_Core_Model_App_State::MODE_DEVELOPER)
            && false == $ignoreDevMode
        ) {
            return false;
        }

        return (bool)(string)$this->_config->getNode(self::XML_PATH_SKIP_PROCESS_MODULES_UPDATES);
    }

    /**
     * Apply database scheme updates whenever needed
     */
    public function updateScheme()
    {
        if (true == $this->_shouldSkipProcessModulesUpdates()) {
            return;
        }

        Magento_Profiler::start('apply_db_schema_updates');
        $this->_appState->setUpdateMode(true);

        $resources = $this->_config->getNode('global/resources')->children();
        $afterApplyUpdates = array();
        foreach ($resources as $resName => $resource) {
            if (!$resource->setup) {
                continue;
            }
            $className = $this->_defaultClass;
            if (isset($resource->setup->class)) {
                $className = $resource->setup->getClassName();
            }

            $setupClass = $this->_factory->create($className, array('resourceName' => $resName));
            $setupClass->applyUpdates();

            if ($setupClass->getCallAfterApplyAllUpdates()) {
                $afterApplyUpdates[] = $setupClass;
            }
        }

        /** @var $setupClass Mage_Core_Model_Resource_SetupInterface*/
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
        $resources = $this->_config->getNode('global/resources')->children();
        foreach ($resources as $resName => $resource) {
            if (!$resource->setup) {
                continue;
            }
            $className = $this->_defaultClass;
            if (isset($resource->setup->class)) {
                $className = $resource->setup->getClassName();
            }
            $setupClass = $this->_factory->create($className, array('resourceName' => $resName));
            $setupClass->applyDataUpdates();
        }
    }
}
