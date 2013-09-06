<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Installer model
 */
class Magento_Install_Model_Installer extends \Magento\Object
{
    /**
     * Installer data model used to store data between installation steps
     *
     * @var \Magento\Object
     */
    protected $_dataModel;

    /**
     * DB updated model
     *
     * @var Magento_Core_Model_Db_UpdaterInterface
     */
    protected $_dbUpdater;

    /**
     * Application chache model
     *
     * @var Magento_Core_Model_CacheInterface
     */
    protected $_cache;

    /**
     * Application config model
     *
     * @var Magento_Core_Model_ConfigInterface
     */
    protected $_config;

    /**
     * @var Magento_Core_Model_Cache_StateInterface
     */
    protected $_cacheState;

    /**
     * @var Magento_Core_Model_Cache_TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @param Magento_Core_Model_ConfigInterface $config
     * @param Magento_Core_Model_Db_UpdaterInterface $dbUpdater
     * @param Magento_Core_Model_CacheInterface $cache
     * @param Magento_Core_Model_Cache_TypeListInterface $cacheTypeList
     * @param Magento_Core_Model_Cache_StateInterface $cacheState
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_ConfigInterface $config,
        Magento_Core_Model_Db_UpdaterInterface $dbUpdater,
        Magento_Core_Model_CacheInterface $cache,
        Magento_Core_Model_Cache_TypeListInterface $cacheTypeList,
        Magento_Core_Model_Cache_StateInterface $cacheState,
        array $data = array()
    ) {
        $this->_dbUpdater = $dbUpdater;
        $this->_config = $config;
        $this->_cache = $cache;
        $this->_cacheState = $cacheState;
        $this->_cacheTypeList = $cacheTypeList;
        parent::__construct($data);
    }

    /**
     * Checking install status of application
     *
     * @return bool
     */
    public function isApplicationInstalled()
    {
        return Mage::isInstalled();
    }

    /**
     * Get data model
     *
     * @return Magento_Core_Model_Session_Generic
     */
    public function getDataModel()
    {
        if (null === $this->_dataModel) {
            $this->setDataModel(Mage::getSingleton('Magento_Install_Model_Session'));
        }
        return $this->_dataModel;
    }

    /**
     * Set data model to store data between installation steps
     *
     * @param \Magento\Object $model
     * @return Magento_Install_Model_Installer
     */
    public function setDataModel(\Magento\Object $model)
    {
        $this->_dataModel = $model;
        return $this;
    }

    /**
     * Check packages (pear) downloads
     *
     * @return boolean
     */
    public function checkDownloads()
    {
        try {
            Mage::getModel('Magento_Install_Model_Installer_Pear')->checkDownloads();
            $result = true;
        } catch (Exception $e) {
            $result = false;
        }
        $this->setDownloadCheckStatus($result);
        return $result;
    }

    /**
     * Check server settings
     *
     * @return bool
     */
    public function checkServer()
    {
        try {
            Mage::getModel('Magento_Install_Model_Installer_Filesystem')->install();
            $result = true;
        } catch (Exception $e) {
            $result = false;
        }
        $this->setData('server_check_status', $result);
        return $result;
    }

    /**
     * Retrieve server checking result status
     *
     * @return bool
     */
    public function getServerCheckStatus()
    {
        $status = $this->getData('server_check_status');
        if (is_null($status)) {
            $status = $this->checkServer();
        }
        return $status;
    }

    /**
     * Installation config data
     *
     * @param   array $data
     * @return  Magento_Install_Model_Installer
     */
    public function installConfig($data)
    {
        $data['db_active'] = true;

        $data = Mage::getSingleton('Magento_Install_Model_Installer_Db')->checkDbConnectionData($data);

        Mage::getSingleton('Magento_Install_Model_Installer_Config')
            ->setConfigData($data)
            ->install();

        /** @var $primaryConfig  Magento_Core_Model_Config_Primary*/
        $primaryConfig = Mage::getSingleton('Magento_Core_Model_Config_Primary');
        $primaryConfig->reinit();

        /** @var $config Magento_Core_Model_Config */
        $config = Mage::getSingleton('Magento_Core_Model_Config');
        $config->reloadConfig();

        return $this;
    }

    /**
     * Database installation
     *
     * @return Magento_Install_Model_Installer
     */
    public function installDb()
    {
        $this->_dbUpdater->updateScheme();
        $data = $this->getDataModel()->getConfigData();

        /**
         * Saving host information into DB
         */
        $setupModel = Mage::getObjectManager()
            ->create('Magento_Core_Model_Resource_Setup', array('resourceName' => 'core_setup'));

        if (!empty($data['use_rewrites'])) {
            $setupModel->setConfigData(Magento_Core_Model_Store::XML_PATH_USE_REWRITES, 1);
        }

        if (!empty($data['enable_charts'])) {
            $setupModel->setConfigData(Magento_Adminhtml_Block_Dashboard::XML_PATH_ENABLE_CHARTS, 1);
        } else {
            $setupModel->setConfigData(Magento_Adminhtml_Block_Dashboard::XML_PATH_ENABLE_CHARTS, 0);
        }

        if (!empty($data['admin_no_form_key'])) {
            $setupModel->setConfigData('admin/security/use_form_key', 0);
        }

        $unsecureBaseUrl = Mage::getBaseUrl('web');
        if (!empty($data['unsecure_base_url'])) {
            $unsecureBaseUrl = $data['unsecure_base_url'];
            $setupModel->setConfigData(Magento_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL, $unsecureBaseUrl);
        }

        if (!empty($data['use_secure'])) {
            $setupModel->setConfigData(Magento_Core_Model_Store::XML_PATH_SECURE_IN_FRONTEND, 1);
            $setupModel->setConfigData(Magento_Core_Model_Store::XML_PATH_SECURE_BASE_URL, $data['secure_base_url']);
            if (!empty($data['use_secure_admin'])) {
                $setupModel->setConfigData(Magento_Core_Model_Store::XML_PATH_SECURE_IN_ADMINHTML, 1);
            }
        } elseif (!empty($data['unsecure_base_url'])) {
            $setupModel->setConfigData(Magento_Core_Model_Store::XML_PATH_SECURE_BASE_URL, $unsecureBaseUrl);
        }

        /**
         * Saving locale information into DB
         */
        $locale = $this->getDataModel()->getLocaleData();
        if (!empty($locale['locale'])) {
            $setupModel->setConfigData(Magento_Core_Model_LocaleInterface::XML_PATH_DEFAULT_LOCALE, $locale['locale']);
        }
        if (!empty($locale['timezone'])) {
            $setupModel->setConfigData(Magento_Core_Model_LocaleInterface::XML_PATH_DEFAULT_TIMEZONE, $locale['timezone']);
        }
        if (!empty($locale['currency'])) {
            $setupModel->setConfigData(Magento_Directory_Model_Currency::XML_PATH_CURRENCY_BASE, $locale['currency']);
            $setupModel->setConfigData(Magento_Directory_Model_Currency::XML_PATH_CURRENCY_DEFAULT, $locale['currency']);
            $setupModel->setConfigData(Magento_Directory_Model_Currency::XML_PATH_CURRENCY_ALLOW, $locale['currency']);
        }

        if (!empty($data['order_increment_prefix'])) {
            $this->_setOrderIncrementPrefix($setupModel, $data['order_increment_prefix']);
        }

        return $this;
    }

    /**
     * Set order number prefix
     *
     * @param Magento_Core_Model_Resource_Setup $setupModel
     * @param string $orderIncrementPrefix
     */
    protected function _setOrderIncrementPrefix(Magento_Core_Model_Resource_Setup $setupModel, $orderIncrementPrefix)
    {
        $select = $setupModel->getConnection()->select()
            ->from($setupModel->getTable('eav_entity_type'), 'entity_type_id')
            ->where('entity_type_code=?', 'order');
        $data = array(
            'entity_type_id' => $setupModel->getConnection()->fetchOne($select),
            'store_id' => '1',
            'increment_prefix' => $orderIncrementPrefix,
        );
        $setupModel->getConnection()->insert($setupModel->getTable('eav_entity_store'), $data);
    }

    /**
     * Create an admin user
     *
     * @param array $data
     */
    public function createAdministrator($data)
    {
        // Magento_User_Model_User belongs to adminhtml area
        Mage::app()->loadAreaPart(Magento_Core_Model_App_Area::AREA_ADMINHTML, Magento_Core_Model_App_Area::PART_CONFIG);
        /** @var $user Magento_User_Model_User */
        $user = Mage::getModel('Magento_User_Model_User');
        $user->loadByUsername($data['username']);
        $user->addData($data)
            ->setForceNewPassword(true) // run-time flag to force saving of the entered password
            ->setRoleId(1)
            ->save();
        $this->_refreshConfig();
    }

    /**
     * Install encryption key into the application, generate and return a random one, if no value is specified
     *
     * @param string $key
     * @return Magento_Install_Model_Installer
     */
    public function installEncryptionKey($key)
    {
        /** @var $helper Magento_Core_Helper_Data */
        $helper = Mage::helper('Magento_Core_Helper_Data');
        $helper->validateKey($key);
        Mage::getSingleton('Magento_Install_Model_Installer_Config')->replaceTmpEncryptKey($key);
        $this->_refreshConfig();
        return $this;
    }

    /**
     * Return a validated encryption key, generating a random one, if no value was initially provided
     *
     * @param string|null $key
     * @return string
     */
    public function getValidEncryptionKey($key = null)
    {
        /** @var $helper Magento_Core_Helper_Data */
        $helper = Mage::helper('Magento_Core_Helper_Data');
        if (!$key) {
            $key = md5($helper->getRandomString(10));
        }
        $helper->validateKey($key);
        return $key;
    }

    /**
     * @return $this
     */
    public function finish()
    {
        Mage::getSingleton('Magento_Install_Model_Installer_Config')->replaceTmpInstallDate();

        /** @var Magento_Core_Model_Config_Primary $primary */
        $primary = Mage::getSingleton('Magento_Core_Model_Config_Primary');
        $primary->reinit();

        $this->_refreshConfig();

        /** @var $config Magento_Core_Model_Config */
        $config = Mage::getSingleton('Magento_Core_Model_Config');
        $config->reloadConfig();

        /* Enable all cache types */
        foreach (array_keys($this->_cacheTypeList->getTypes()) as $cacheTypeCode) {
            $this->_cacheState->setEnabled($cacheTypeCode, true);
        }
        $this->_cacheState->persist();
        return $this;
    }

    /**
     * Ensure changes in the configuration, if any, take effect
     */
    protected function _refreshConfig()
    {
        $this->_cache->clean();
        $this->_config->reinit();
    }
}
