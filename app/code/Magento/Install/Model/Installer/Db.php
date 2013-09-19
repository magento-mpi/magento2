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
     * @var database resource
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
     * Databases configuration
     * 
     * @var array
     */
    protected $_dbConfig;

    /**
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Config_Resource $resourceConfig
     * @param array $dbConfig
     */
    public function __construct(
        Magento_Core_Model_Logger $logger, 
        Magento_Core_Model_Config_Resource $resourceConfig,
        array $dbConfig
    )
    {
        $this->_resourceConfig = $resourceConfig;
        $this->_logger = $logger;
        $this->_dbConfig = $dbConfig;
    }

    /**
     * Check database connection
     * and return checked connection data
     *
     * @param array $data
     * @return array
     */
    public function checkDbConnectionData($data)
    {
        $data = $this->_getCheckedData($data);

        try {
            $dbModel = ($data['db_model']);

            if (!$resource = $this->_getDbResource($dbModel)) {
                Mage::throwException(
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
                Mage::throwException(
                    __('PHP Extensions "%1" must be loaded.', implode(',', $absenteeExtensions))
                );
            }

            $version    = $resource->getVersion();
            $requiredVersion = isset($this->_dbConfig[$dbModel]['min_version'])
                ? $this->_dbConfig[$dbModel]['min_version']
                : 0;

            // check DB server version
            if (version_compare($version, $requiredVersion) == -1) {
                Mage::throwException(
                    __('The database server version doesn\'t match system requirements (required: %1, actual: %2).', $requiredVersion, $version)
                );
            }

            // check InnoDB support
            if (!$resource->supportEngine()) {
                Mage::throwException(
                    __('Database server does not support the InnoDB storage engine.')
                );
            }

            // TODO: check user roles
        } catch (Magento_Core_Exception $e) {
            $this->_logger->logException($e);
            Mage::throwException(__($e->getMessage()));
        } catch (Exception $e) {
            $this->_logger->logException($e);
            Mage::throwException(__('Something went wrong while connecting to the database.'));
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
            Mage::throwException(__('The Database Name field cannot be empty.'));
        }
        //make all table prefix to lower letter
        if ($data['db_prefix'] != '') {
            $data['db_prefix'] = strtolower($data['db_prefix']);
        }
        //check table prefix
        if ($data['db_prefix'] != '') {
            if (!preg_match('/^[a-z]+[a-z0-9_]*$/', $data['db_prefix'])) {
                Mage::throwException(
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
            $data['db_type'] = isset($this->_dbConfig[$data['db_model']]['type'])
                ? $this->_dbConfig[$data['db_model']]['type']
                : null;
        }

        $dbResource = $this->_getDbResource($data['db_model']);
        $data['db_pdo_type'] = $dbResource->getPdoType();

        if (!isset($data['db_init_statements'])) {
            $data['db_init_statements'] = isset($this->_dbConfig[$data['db_model']]['initStatements'])
                ? $this->_dbConfig[$data['db_model']]['initStatements']
                : null;
        }

        return $data;
    }

    /**
     * Retrieve the database resource
     *
     * @param  string $model database type
     * @return Magento_Install_Model_Installer_Db_Abstract
     */
    protected function _getDbResource($model)
    {
        if (!isset($this->_dbResource)) {
            $resource =  Mage::getSingleton("Magento_Install_Model_Installer_Db_" . ucfirst($model));
            if (!$resource) {
                Mage::throwException(
                    __('Installer does not exist for %1 database type', $model)
                );
            }
            $this->_dbResource = $resource;
        }
        return $this->_dbResource;
    }
}
