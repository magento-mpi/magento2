<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend config model
 * Used to save configuration
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Magento_Backend_Model_Config extends Magento_Object
{
    /**
     * Config data for sections
     *
     * @var array
     */
    protected $_configData;

    /**
     * Event dispatcher
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * System configuration structure
     *
     * @var Magento_Backend_Model_Config_Structure
     */
    protected $_configStructure;

    /**
     * Application config
     *
     * @var Magento_Core_Model_Config
     */
    protected $_appConfig;

    /**
     * Global factory
     *
     * @var Magento_Core_Model_Config
     */
    protected $_objectFactory;

    /**
     * TransactionFactory
     *
     * @var Magento_Core_Model_Resource_TransactionFactory
     */
    protected $_transactionFactory;

    /**
     * Global Application
     *
     * @var Magento_Core_Model_App
     */
    protected $_application;

    /**
     * Config data loader
     *
     * @var Magento_Backend_Model_Config_Loader
     */
    protected $_configLoader;

    /**
     * Config data factory
     *
     * @var Magento_Core_Model_Config_ValueFactory
     */
    protected $_configValueFactory;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Model_App $application
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Backend_Model_Config_Structure $configStructure
     * @param Magento_Core_Model_Resource_TransactionFactory $transactionFactory
     * @param Magento_Backend_Model_Config_Loader $configLoader
     * @param Magento_Core_Model_Config_ValueFactory $configValueFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_App $application,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Backend_Model_Config_Structure $configStructure,
        Magento_Core_Model_Resource_TransactionFactory $transactionFactory,
        Magento_Backend_Model_Config_Loader $configLoader,
        Magento_Core_Model_Config_ValueFactory $configValueFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        parent::__construct($data);
        $this->_eventManager = $eventManager;
        $this->_configStructure = $configStructure;
        $this->_transactionFactory = $transactionFactory;
        $this->_appConfig = $config;
        $this->_application = $application;
        $this->_configLoader = $configLoader;
        $this->_configValueFactory = $configValueFactory;
        $this->_storeManager = $storeManager;
    }

    /**
     * Save config section
     * Require set: section, website, store and groups
     *
     * @throws Exception
     * @return Magento_Backend_Model_Config
     */
    public function save()
    {
        $this->_validate();
        $this->_getScope();

        $this->_eventManager->dispatch('model_config_data_save_before', array('object' => $this));

        $sectionId = $this->getSection();
        $groups  = $this->getGroups();
        if (empty($groups)) {
            return $this;
        }

        $oldConfig = $this->_getConfig(true);

        $deleteTransaction = $this->_transactionFactory->create();
        /* @var $deleteTransaction Magento_Core_Model_Resource_Transaction */
        $saveTransaction = $this->_transactionFactory->create();
        /* @var $saveTransaction Magento_Core_Model_Resource_Transaction */

        // Extends for old config data
        $extraOldGroups = array();

        foreach ($groups as $groupId => $groupData) {
            $this->_processGroup(
                $groupId, $groupData, $groups, $sectionId, $extraOldGroups, $oldConfig,
                $saveTransaction, $deleteTransaction
            );
        }

        try {
            $deleteTransaction->delete();
            $saveTransaction->save();

            // re-init configuration
            $this->_eventManager->dispatch('application_process_reinit_config');
            $this->_storeManager->reinitStores();

            $this->_eventManager->dispatch('admin_system_config_section_save_after', array(
                'website' => $this->getWebsite(),
                'store' => $this->getStore(),
                'section' => $this->getSection()
            ));

            // website and store codes can be used in event implementation, so set them as well
            $this->_eventManager->dispatch("admin_system_config_changed_section_{$this->getSection()}", array(
                'website' => $this->getWebsite(),
                'store' => $this->getStore()
            ));
        } catch (Exception $e) {
            // re-init configuration
            $this->_eventManager->dispatch('application_process_reinit_config');
            $this->_storeManager->reinitStores();
            throw $e;
        }

        return $this;
    }

    /**
     * Process group data
     *
     * @param string $groupId
     * @param array $groupData
     * @param array $groups
     * @param string $sectionPath
     * @param array $extraOldGroups
     * @param array $oldConfig
     * @param Magento_Core_Model_Resource_Transaction $saveTransaction
     * @param Magento_Core_Model_Resource_Transaction $deleteTransaction
     */
    protected function _processGroup(
        $groupId,
        array $groupData,
        array $groups,
        $sectionPath,
        array &$extraOldGroups,
        array &$oldConfig,
        Magento_Core_Model_Resource_Transaction $saveTransaction,
        Magento_Core_Model_Resource_Transaction $deleteTransaction
    ) {
        $groupPath = $sectionPath . '/' . $groupId;
        $website = $this->getWebsite();
        $store = $this->getStore();
        $scope = $this->getScope();
        $scopeId = $this->getScopeId();
        /**
         *
         * Map field names if they were cloned
         */
        /** @var $group Magento_Backend_Model_Config_Structure_Element_Group */
        $group = $this->_configStructure->getElement($groupPath);


        // set value for group field entry by fieldname
        // use extra memory
        $fieldsetData = array();
        if (isset($groupData['fields'])) {
            if ($group->shouldCloneFields()) {
                $cloneModel = $group->getCloneModel();
                $mappedFields = array();

                /** @var $field Magento_Backend_Model_Config_Structure_Element_Field */
                foreach ($group->getChildren() as $field) {
                    foreach ($cloneModel->getPrefixes() as $prefix) {
                        $mappedFields[$prefix['field'] . $field->getId()] = $field->getId();
                    }
                }
            }
            foreach ($groupData['fields'] as $fieldId => $fieldData) {
                $fieldsetData[$fieldId] = (is_array($fieldData) && isset($fieldData['value']))
                    ? $fieldData['value'] : null;
            }

            foreach ($groupData['fields'] as $fieldId => $fieldData) {
                $originalFieldId = $fieldId;
                if ($group->shouldCloneFields() && isset($mappedFields[$fieldId])) {
                    $originalFieldId = $mappedFields[$fieldId];
                }
                /** @var $field Magento_Backend_Model_Config_Structure_Element_Field */
                $field = $this->_configStructure->getElement($groupPath . '/' . $originalFieldId);

                /** @var Magento_Core_Model_Config_Data $backendModel */
                $backendModel = $field->hasBackendModel() ?
                    $field->getBackendModel() :
                    $this->_configValueFactory->create();

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
                        $configGroupPath = substr($configPath, 0, strrpos($configPath, '/'));
                        if (!isset($extraOldGroups[$configGroupPath])) {
                            $oldConfig = $this->extendConfig($configGroupPath, true, $oldConfig);
                            $extraOldGroups[$configGroupPath] = true;
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

        if (isset($groupData['groups'])) {
            foreach ($groupData['groups'] as $subGroupId => $subGroupData) {
                $this->_processGroup(
                    $subGroupId, $subGroupData, $groups, $groupPath, $extraOldGroups,
                    $oldConfig, $saveTransaction, $deleteTransaction
                );
            }
        }
    }

    /**
     * Load config data for section
     *
     * @return array
     */
    public function load()
    {
        if (is_null($this->_configData)) {
            $this->_validate();
            $this->_getScope();
            $this->_configData = $this->_getConfig(false);
        }
        return $this->_configData;
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
            $store = $this->_storeManager->getStore($this->getStore());
            $scopeId = (int)$store->getId();
            $scopeCode = $this->getStore();
        } elseif ($this->getWebsite()) {
            $scope   = 'websites';
            $website = $this->_storeManager->getWebsite($this->getWebsite());
            $scopeId = (int)$website->getId();
            $scopeCode = $this->getWebsite();
        } else {
            $scope   = 'default';
            $scopeId = 0;
            $scopeCode = '';
        }
        $this->setScope($scope);
        $this->setScopeId($scopeId);
        $this->setScopeCode($scopeCode);
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
     * @param Magento_Backend_Model_Config_Structure_Element_Field $fieldConfig
     * @param Magento_Core_Model_Config_Data $dataObject
     */
    protected function _checkSingleStoreMode(
        Magento_Backend_Model_Config_Structure_Element_Field $fieldConfig,
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

    /**
     * Get config data value
     *
     * @param string $path
     * @param null|bool $inherit
     * @param null|array $configData
     * @return Magento_Simplexml_Element
     */
    public function getConfigDataValue($path, &$inherit = null, $configData = null)
    {
        $this->load();
        if (is_null($configData)) {
            $configData = $this->_configData;
        }
        if (isset($configData[$path])) {
            $data = $configData[$path];
            $inherit = false;
        } else {
            $data =  $this->_appConfig->getValue($path, $this->getScope(), $this->getScopeCode());
            $inherit = true;
        }

        return $data;
    }
}
