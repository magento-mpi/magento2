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
 * Magento application console installer
 */
namespace Magento\Install\Model\Installer;

class Console extends \Magento\Install\Model\Installer\AbstractInstaller
{
    /**#@+
     * Installation options for application initialization
     */
    const OPTION_URIS = 'install_option_uris';
    const OPTION_DIRS = 'install_option_dirs';
    /**#@- */

    /**
     * Available installation options
     *
     * @var array
     */
    protected $_installOptions = array(
        'license_agreement_accepted' => array('required' => 1),
        'locale'                     => array('required' => 1),
        'timezone'                   => array('required' => 1),
        'default_currency'           => array('required' => 1),
        'db_model'                   => array('required' => 0),
        'db_host'                    => array('required' => 1),
        'db_name'                    => array('required' => 1),
        'db_user'                    => array('required' => 1),
        'db_pass'                    => array('required' => 0),
        'db_prefix'                  => array('required' => 0),
        'url'                        => array('required' => 1),
        'skip_url_validation'        => array('required' => 0),
        'use_rewrites'               => array('required' => 1),
        'use_secure'                 => array('required' => 1),
        'secure_base_url'            => array('required' => 1),
        'use_secure_admin'           => array('required' => 1),
        'admin_lastname'             => array('required' => 1),
        'admin_firstname'            => array('required' => 1),
        'admin_email'                => array('required' => 1),
        'admin_username'             => array('required' => 1),
        'admin_password'             => array('required' => 1),
        'admin_no_form_key'          => array('required' => 0),
        'encryption_key'             => array('required' => 0),
        'session_save'               => array('required' => 0),
        'backend_frontname'          => array('required' => 0),
        'enable_charts'              => array('required' => 0),
        'order_increment_prefix'     => array('required' => 0),
        'cleanup_database'           => array('required' => 0),
    );

    /**
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * Installer data model to store data between installations steps
     *
     * @var \Magento\Install\Model\Installer\Data|\Magento\Core\Model\Session\Generic
     */
    protected $_dataModel;

    /**
     * DB updater
     *
     * @var \Magento\Core\Model\Db\UpdaterInterface
     */
    protected $_dbUpdater;

    /**
     * @param \Magento\Core\Model\Db\UpdaterInterface $daUpdater
     * @param \Magento\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Core\Model\Db\UpdaterInterface $daUpdater,
        \Magento\Filesystem $filesystem
    ) {
        $this->_dbUpdater = $daUpdater;
        $this->_getInstaller()->setDataModel($this->_getDataModel());
        $this->_filesystem = $filesystem;
    }

    /**
     * Retrieve validated installation options
     *
     * @param array $options
     * @return array|boolean
     */
    protected function _getInstallOptions(array $options)
    {
        /**
         * Check required options
         */
        foreach ($this->_installOptions as $optionName => $optionInfo) {
            if (isset($optionInfo['required']) && $optionInfo['required'] && !isset($options[$optionName])) {
                $this->addError("ERROR: installation option '$optionName' is required.");
            }
        }

        if ($this->hasErrors()) {
            return false;
        }

        /**
         * Validate license agreement acceptance
         */
        if (!$this->_getFlagValue($options['license_agreement_accepted'])) {
            $this->addError(
                'ERROR: You have to accept Magento license agreement terms and conditions to continue installation.'
            );
            return false;
        }

        $result = array();
        foreach ($this->_installOptions as $optionName => $optionInfo) {
            $result[$optionName] = isset($options[$optionName]) ? $options[$optionName] : '';
        }

        return $result;
    }

    /**
     * Add error
     *
     * @param string $error
     * @return \Magento\Install\Model\Installer\Console
     */
    public function addError($error)
    {
        $this->_getDataModel()->addError($error);
        return $this;
    }

    /**
     * Check if there were any errors
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return (count($this->_getDataModel()->getErrors()) > 0);
    }

    /**
     * Get all errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_getDataModel()->getErrors();
    }

    /**
     * Return TRUE for 'yes', 1, 'true' (case insensitive) or FALSE otherwise
     *
     * @param string $value
     * @return boolean
     */
    protected function _getFlagValue($value)
    {
        $res = (1 == $value) || preg_match('/^(yes|y|true)$/i', $value);
        return $res;
    }

    /**
     * Get data model (used to store data between installation steps
     *
     * @return \Magento\Install\Model\Installer\Data
     */
    protected function _getDataModel()
    {
        if (null === $this->_dataModel) {
            $this->_dataModel = \Mage::getModel('Magento\Install\Model\Installer\Data');
        }
        return $this->_dataModel;
    }

    /**
     * Install Magento
     *
     * @param array $options
     * @return string|boolean
     */
    public function install(array $options)
    {
        try {
            $options = $this->_getInstallOptions($options);
            if (!$options) {
                return false;
            }

            /**
             * Check if already installed
             */
            if (\Mage::isInstalled()) {
                $this->addError('ERROR: Magento is already installed.');
                return false;
            }

            /**
             * Skip URL validation, if set
             */
            $this->_getDataModel()->setSkipUrlValidation($options['skip_url_validation']);
            $this->_getDataModel()->setSkipBaseUrlValidation($options['skip_url_validation']);

            /**
             * Locale settings
             */
            $this->_getDataModel()->setLocaleData(array(
                'locale'            => $options['locale'],
                'timezone'          => $options['timezone'],
                'currency'          => $options['default_currency'],
            ));

            /**
             * Database and web config
             */
            $this->_getDataModel()->setConfigData(array(
                'db_model'               => $options['db_model'],
                'db_host'                => $options['db_host'],
                'db_name'                => $options['db_name'],
                'db_user'                => $options['db_user'],
                'db_pass'                => $options['db_pass'],
                'db_prefix'              => $options['db_prefix'],
                'use_rewrites'           => $this->_getFlagValue($options['use_rewrites']),
                'use_secure'             => $this->_getFlagValue($options['use_secure']),
                'unsecure_base_url'      => $options['url'],
                'secure_base_url'        => $options['secure_base_url'],
                'use_secure_admin'       => $this->_getFlagValue($options['use_secure_admin']),
                'session_save'           => $this->_checkSessionSave($options['session_save']),
                'backend_frontname'      => $this->_checkBackendFrontname($options['backend_frontname']),
                'admin_no_form_key'      => $this->_getFlagValue($options['admin_no_form_key']),
                'skip_url_validation'    => $this->_getFlagValue($options['skip_url_validation']),
                'enable_charts'          => $this->_getFlagValue($options['enable_charts']),
                'order_increment_prefix' => $options['order_increment_prefix'],
            ));

            /**
             * Primary admin user
             */
            $this->_getDataModel()->setAdminData(array(
                'firstname'         => $options['admin_firstname'],
                'lastname'          => $options['admin_lastname'],
                'email'             => $options['admin_email'],
                'username'          => $options['admin_username'],
                'password'          => $options['admin_password'],
            ));

            $installer = $this->_getInstaller();

            /**
             * Install configuration
             */
            $installer->installConfig($this->_getDataModel()->getConfigData());

            if (!empty($options['cleanup_database'])) {
                $this->_cleanUpDatabase();
            }

            if ($this->hasErrors()) {
                return false;
            }

            /**
             * Install database
             */
            $installer->installDb();

            if ($this->hasErrors()) {
                return false;
            }

            // apply data updates
            $this->_dbUpdater->updateData();

            /**
             * Create primary administrator user & install encryption key
             */
            $encryptionKey = !empty($options['encryption_key']) ? $options['encryption_key'] : null;
            $encryptionKey = $installer->getValidEncryptionKey($encryptionKey);
            $installer->createAdministrator($this->_getDataModel()->getAdminData());
            $installer->installEncryptionKey($encryptionKey);

            /**
             * Installation finish
             */
            $installer->finish();

            if ($this->hasErrors()) {
                return false;
            }

            /**
             * Change directories mode to be writable by apache user
             */
            $this->_filesystem->changePermissions(\Mage::getBaseDir('var'), 0777, true);
            return $encryptionKey;
        } catch (\Exception $e) {
            if ($e instanceof \Magento\Core\Exception) {
                foreach ($e->getMessages(\Magento\Core\Model\Message::ERROR) as $errorMessage) {
                    $this->addError($errorMessage);
                }
            } else {
                $this->addError('ERROR: ' . $e->getMessage());
            }
            return false;
        }
    }

    /**
     * Cleanup database use system configuration
     */
    protected function _cleanUpDatabase()
    {
        $modelName = 'Magento\Install\Model\Installer\Db\Mysql4';
        /** @var $resourceModel \Magento\Install\Model\Installer\Db\AbstractDb */
        $resourceModel = \Mage::getModel($modelName);
        $resourceModel->cleanUpDatabase();
    }

    /**
     * Uninstall the application
     *
     * @return bool
     */
    public function uninstall()
    {
        if (!\Mage::isInstalled()) {
            return false;
        }

        $this->_cleanUpDatabase();

        /* Remove temporary directories and local.xml */
        foreach (glob(\Mage::getBaseDir(\Magento\Core\Model\Dir::VAR_DIR) . '/*', GLOB_ONLYDIR) as $dir) {
            $this->_filesystem->delete($dir);
        }
        $this->_filesystem->delete(\Mage::getBaseDir(\Magento\Core\Model\Dir::CONFIG) . DIRECTORY_SEPARATOR . '/local.xml');
        return true;
    }

    /**
     * Retrieve available locale codes
     *
     * @return array
     */
    public function getAvailableLocales()
    {
        return \Mage::app()->getLocale()->getOptionLocales();
    }

    /**
     * Retrieve available currency codes
     *
     * @return array
     */
    public function getAvailableCurrencies()
    {
        return \Mage::app()->getLocale()->getOptionCurrencies();
    }

    /**
     * Retrieve available timezone codes
     *
     * @return array
     */
    public function getAvailableTimezones()
    {
        return \Mage::app()->getLocale()->getOptionTimezones();
    }

    /**
     * Retrieve available installation options
     *
     * @return array
     */
    public function getAvailableInstallOptions()
    {
        $result = array();
        foreach ($this->_installOptions as $optionName => $optionInfo) {
            $result[$optionName] = ($optionInfo['required'] ? 'required' : 'optional');
        }
        return $result;
    }
}
