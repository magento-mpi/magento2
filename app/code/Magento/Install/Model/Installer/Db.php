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
 * DB Installer
 */
class Magento_Install_Model_Installer_Db extends Magento_Install_Model_Installer_Abstract
{
    /**
     * Database resource
     *
     * @var Magento_Install_Model_Installer_Db_Abstract
     */
    protected $_dbResource;

    /**
     * Resource configuration
     *
     * @var Magento_Core_Model_Config_Resource
     */
    protected $_resourceConfig;

    /**
     * @var Magento_Core_Model_Logger
     */
    protected $_logger;

    /**
     * Database model factory
     * 
     * @var Magento_Install_Model_Installer_Db_Factory
     */
    protected $_dbFactory;

    /**
     * Databases configuration
     *
     * @var array
     */
    protected $_dbConfig;

    /**
     * @param Magento_Install_Model_InstallerProxy $installer
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Config_Resource $resourceConfig
     * @param Magento_Install_Model_Installer_Db_Factory $dbFactory
     * @param array $dbConfig
     */
    public function __construct(
        Magento_Install_Model_InstallerProxy $installer,
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Config_Resource $resourceConfig,
        Magento_Install_Model_Installer_Db_Factory $dbFactory,
        array $dbConfig
    ) {
        parent::__construct($installer);
        $this->_resourceConfig = $resourceConfig;
        $this->_logger = $logger;
        $this->_dbConfig = $dbConfig;
        $this->_dbFactory = $dbFactory;
    }

    /**
     * Check database connection
     * and return checked connection data
     *
     * @param array $data
     * @return array
     * @throws Magento_Core_Exception
     */
    public function checkDbConnectionData($data)
    {
        $data = $this->_getCheckedData($data);

        try {
            $dbModel = ($data['db_model']);

            if (!$resource = $this->_getDbResource($dbModel)) {
                throw new Magento_Core_Exception(
                    __('There is no resource for %1 DB model.', $dbModel)
                );
            }

            $resource->setConfig($data);

            // check required extensions
            $absenteeExtensions = array();
            $extensions = $resource->getRequiredExtensions();
            foreach ($extensions as $extName) {
                if (!extension_loaded($extName)) {
                    $absenteeExtensions[] = $extName;
                }
            }
            if (!empty($absenteeExtensions)) {
                throw new Magento_Core_Exception(
                    __('PHP Extensions "%1" must be loaded.', implode(',', $absenteeExtensions))
                );
            }

            $version    = $resource->getVersion();
            $requiredVersion = isset($this->_dbConfig[$dbModel]['min_version'])
                ? $this->_dbConfig[$dbModel]['min_version']
                : 0;

            // check DB server version
            if (version_compare($version, $requiredVersion) == -1) {
                throw new Magento_Core_Exception(
                    __('The database server version doesn\'t match system requirements (required: %1, actual: %2).', $requiredVersion, $version)
                );
            }

            // check InnoDB support
            if (!$resource->supportEngine()) {
                throw new Magento_Core_Exception(
                    __('Database server does not support the InnoDB storage engine.')
                );
            }

            // TODO: check user roles
        } catch (Magento_Core_Exception $e) {
            $this->_logger->logException($e);
            throw new Magento_Core_Exception(__($e->getMessage()));
        } catch (Exception $e) {
            $this->_logger->logException($e);
            throw new Magento_Core_Exception(__('Something went wrong while connecting to the database.'));
        }

        return $data;
    }

    /**
     * Check database connection data
     *
     * @param  array $data
     * @return array
     */
    protected function _getCheckedData($data)
    {
        if (!isset($data['db_name']) || empty($data['db_name'])) {
            throw new Magento_Core_Exception(__('The Database Name field cannot be empty.'));
        }
        //make all table prefix to lower letter
        if ($data['db_prefix'] != '') {
            $data['db_prefix'] = strtolower($data['db_prefix']);
        }
        //check table prefix
        if ($data['db_prefix'] != '') {
            if (!preg_match('/^[a-z]+[a-z0-9_]*$/', $data['db_prefix'])) {
                throw new Magento_Core_Exception(
                    __('The table prefix should contain only letters (a-z), numbers (0-9) or underscores (_); the first character should be a letter.')
                );
            }
        }
        //set default db model
        if (!isset($data['db_model']) || empty($data['db_model'])) {
            $data['db_model'] = $this->_resourceConfig
                ->getResourceConnectionConfig(Magento_Core_Model_Resource::DEFAULT_SETUP_RESOURCE)->model;
        }
        //set db type according the db model
        if (!isset($data['db_type'])) {
            $data['db_type'] = isset($this->_dbConfig[(string)$data['db_model']]['type'])
                ? $this->_dbConfig[(string)$data['db_model']]['type']
                : null;
        }

        $dbResource = $this->_getDbResource($data['db_model']);
        $data['db_pdo_type'] = $dbResource->getPdoType();

        if (!isset($data['db_init_statements'])) {
            $data['db_init_statements'] = isset($this->_dbConfig[(string)$data['db_model']]['initStatements'])
                ? $this->_dbConfig[(string)$data['db_model']]['initStatements']
                : null;
        }

        return $data;
    }

    /**
     * Retrieve the database resource
     *
     * @param  string $model database type
     * @return Magento_Install_Model_Installer_Db_Abstract
     * @throws Magento_Core_Exception
     */
    protected function _getDbResource($model)
    {
        if (!isset($this->_dbResource)) {
            $resource =  $this->_dbFactory->get($model);
            if (!$resource) {
                throw new Magento_Core_Exception(
                    __('Installer does not exist for %1 database type', $model)
                );
            }
            $this->_dbResource = $resource;
        }
        return $this->_dbResource;
    }
}
