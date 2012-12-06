<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend config model
 * Used to save configuration
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Backend_Model_Config extends Varien_Object
{
    /**
     * Event dispatcher
     *
     * @var Mage_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * System configuration structure
     *
     * @var Mage_Backend_Model_Config_Structure
     */
    protected $_configStructure;

    /**
     * Application config
     *
     * @var Mage_Core_Model_Config
     */
    protected $_appConfig;

    /**
     * Global factory
     *
     * @var Mage_Core_Model_Config
     */
    protected $_objectFactory;

    /**
     * TransactionFactory
     *
     * @var Mage_Core_Model_Resource_Transaction_Factory
     */
    protected $_transactionFactory;

    /**
     * Global Application
     *
     * @var Mage_Core_Model_App
     */
    protected $_application;

    /**
     * Config data loader
     *
     * @var Mage_Backend_Model_Config_Loader
     */
    protected $_configLoader;

    /**
     * Config data factory
     *
     * @var Mage_Backend_Model_Config_Loader
     */
    protected $_configDataFactory;

    /**
     * @param Mage_Core_Model_App $application
     * @param Mage_Core_Model_Config $config
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Mage_Backend_Model_Config_Structure $configStructure
     * @param Mage_Core_Model_Resource_Transaction_Factory $transactionFactory
     * @param Mage_Backend_Model_Config_Loader $configLoader
     * @param Mage_Core_Model_Config_Data_Factory $configDataFactory
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_App $application,
        Mage_Core_Model_Config $config,
        Mage_Core_Model_Event_Manager $eventManager,
        Mage_Backend_Model_Config_Structure $configStructure,
        Mage_Core_Model_Resource_Transaction_Factory $transactionFactory,
        Mage_Backend_Model_Config_Loader $configLoader,
        Mage_Core_Model_Config_Data_Factory $configDataFactory,
        array $data = array()
    ) {
        $this->_eventManager = $eventManager;
        $this->_configStructure = $configStructure;
        $this->_transactionFactory = $transactionFactory;
        $this->_appConfig = $config;
        $this->_application = $application;
        $this->_configLoader = $configLoader;
        $this->_configDataFactory = $configDataFactory;
        parent::__construct($data);
    }

    /**
     * Save config section
     * Require set: section, website, store and groups
     *
     * @return Mage_Backend_Model_Config
     */
    public function save()
    {
        $this->_validate();
        $this->_getScope();

        $this->_eventManager->dispatch('model_config_data_save_before', array('object' => $this));

        $sectionId = $this->getSection();
        $website = $this->getWebsite();
        $store   = $this->getStore();
        $groups  = $this->getGroups();
        $scope   = $this->getScope();
        $scopeId = $this->getScopeId();

        if (empty($groups)) {
            return $this;
        }

        $oldConfig = $this->_getConfig(true);

        $deleteTransaction = $this->_transactionFactory->create();
        /* @var $deleteTransaction Mage_Core_Model_Resource_Transaction */
        $saveTransaction = $this->_transactionFactory->create();
        /* @var $saveTransaction Mage_Core_Model_Resource_Transaction */

        // Extends for old config data
        $extraOldGroups = array();
        $mappedFields = array();

        foreach ($groups as $groupId => $groupData) {
            /**
             * Map field names if they were cloned
             */
            /** @var $group Mage_Backend_Model_Config_Structure_Element_Group */
            $group = $this->_configStructure->getElementByPathParts(array($sectionId, $groupId));

            if ($group->shouldCloneFields()) {
                $cloneModel = $group->getCloneModel();
                $mappedFields = array();

                /** @var $field Mage_Backend_Model_Config_Structure_Element_Field */
                foreach ($group->getChildren() as $field) {
                    foreach ($cloneModel->getPrefixes() as $prefix) {
                        $mappedFields[$prefix['field'] . $field->getId()] = $field->getId();
                    }
                }
            }
            // set value for group field entry by fieldname
            // use extra memory
            $fieldsetData = array();
            foreach ($groupData['fields'] as $fieldId => $fieldData) {
                $fieldsetData[$fieldId] = (is_array($fieldData) && isset($fieldData['value']))
                    ? $fieldData['value'] : null;
            }

            foreach ($groupData['fields'] as $fieldId => $fieldData) {
                $originalFieldId = $fieldId;
                if ($group->shouldCloneFields() && isset($mappedFields[$fieldId])) {
                    $originalFieldId = $mappedFields[$fieldId];
                }
                /** @var $field Mage_Backend_Model_Config_Structure_Element_Field */
                $field = $this->_configStructure->getElementByPathParts(
                    array($sectionId, $group->getId(), $originalFieldId)
                );

                /** @var Mage_Core_Model_Config_Data $backendModel  */
                $backendModel = $field->hasBackendModel() ?
                    $field->getBackendModel() :
                    $this->_configDataFactory->create();

                $data = array(
                    'field' => $fieldId,
                    'groups' => $groups,
                    'group_id' => $group->getId(),
                    'store_code' => $store,
                    'website_code' => $website,
                    'scope' => $scope,
                    'scope_id' => $scopeId,
                    'field_config' => $field->getData(),
                    'fieldset_data' => $fieldsetData,
                );
                $backendModel->addData($data);

                $this->_checkSingleStoreMode($field, $backendModel);

                if (false == isset($fieldData['value'])) {
                    $fieldData['value'] = null;
                }

                $path = $field->getGroupPath() . '/' . $fieldId;
                /**
                 * Look for custom defined field path
                 */
                if ($field && $field->getConfigPath()) {
                    $configPath = $field->getConfigPath();
                    if (!empty($configPath) && strrpos($configPath, '/') > 0) {
                        // Extend old data with specified section group
                        $groupPath = substr($configPath, 0, strrpos($configPath, '/'));
                        if (!isset($extraOldGroups[$groupPath])) {
                            $oldConfig = $this->extendConfig($groupPath, true, $oldConfig);
                            $extraOldGroups[$groupPath] = true;
                        }
                        $path = $configPath;
                    }
                }

                $inherit = !empty($fieldData['inherit']);

                $backendModel->setPath($path)->setValue($fieldData['value']);

                if (isset($oldConfig[$path])) {
                    $backendModel->setConfigId($oldConfig[$path]['config_id']);

                    /**
                     * Delete config data if inherit
                     */
                    if (!$inherit) {
                        $saveTransaction->addObject($backendModel);
                    } else {
                        $deleteTransaction->addObject($backendModel);
                    }
                } elseif (!$inherit) {
                    $backendModel->unsConfigId();
                    $saveTransaction->addObject($backendModel);
                }
            }

        }

        $deleteTransaction->delete();
        $saveTransaction->save();

        return $this;
    }

    /**
     * Load config data for section
     *
     * @return array
     */
    public function load()
    {
        $this->_validate();
        $this->_getScope();

        return $this->_getConfig(false);
    }

    /**
     * Extend config data with additional config data by specified path
     *
     * @param string $path Config path prefix
     * @param bool $full Simple config structure or not
     * @param array $oldConfig Config data to extend
     * @return array
     */
    public function extendConfig($path, $full = true, $oldConfig = array())
    {
        $extended = $this->_configLoader->getConfigByPath($path, $this->getScope(), $this->getScopeId(), $full);
        if (is_array($oldConfig) && !empty($oldConfig)) {
            return $oldConfig + $extended;
        }
        return $extended;
    }

    /**
     * Validate isset required parametrs
     *
     */
    protected function _validate()
    {
        if (is_null($this->getSection())) {
            $this->setSection('');
        }
        if (is_null($this->getWebsite())) {
            $this->setWebsite('');
        }
        if (is_null($this->getStore())) {
            $this->setStore('');
        }
    }

    /**
     * Get scope name and scopeId
     *
     */
    protected function _getScope()
    {
        if ($this->getStore()) {
            $scope   = 'stores';
            $scopeId = (int) $this->_appConfig->getNode('stores/' . $this->getStore() . '/system/store/id');
        } elseif ($this->getWebsite()) {
            $scope   = 'websites';
            $scopeId = (int) $this->_appConfig->getNode('websites/' . $this->getWebsite() . '/system/website/id');
        } else {
            $scope   = 'default';
            $scopeId = 0;
        }
        $this->setScope($scope);
        $this->setScopeId($scopeId);
    }

    /**
     * Return formatted config data for current section
     *
     * @param bool $full Simple config structure or not
     * @return array
     */
    protected function _getConfig($full = true)
    {
        return $this->_configLoader->getConfigByPath(
            $this->getSection(), $this->getScope(), $this->getScopeId(), $full
        );
    }

    /**
     * Set correct scope if isSingleStoreMode = true
     *
     * @param Mage_Backend_Model_Config_Structure_Element_Field $fieldConfig
     * @param Mage_Core_Model_Config_Data $dataObject
     */
    protected function _checkSingleStoreMode(
        Mage_Backend_Model_Config_Structure_Element_Field $fieldConfig,
        $dataObject
    ) {
        $isSingleStoreMode = $this->_application->isSingleStoreMode();
        if (!$isSingleStoreMode) {
            return;
        }
        if (!$fieldConfig->showInDefault()) {
            $websites = $this->_application->getWebsites();
            $singleStoreWebsite = array_shift($websites);
            $dataObject->setScope('websites');
            $dataObject->setWebsiteCode($singleStoreWebsite->getCode());
            $dataObject->setScopeId($singleStoreWebsite->getId());
        }
    }
}
