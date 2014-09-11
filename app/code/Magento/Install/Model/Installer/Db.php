<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * DB Installer
 */
namespace Magento\Install\Model\Installer;

class Db
{
    /**
     * Database resource
     *
     * @var \Magento\Install\Model\Installer\Db\AbstractDb
     */
    protected $_dbResource;

    /**
     * @var \Magento\Framework\Logger
     */
    protected $_logger;

    /**
     * Database model factory
     *
     * @var \Magento\Install\Model\Installer\Db\Factory
     */
    protected $_dbFactory;

    /**
     * Databases configuration
     *
     * @var array
     */
    protected $_dbConfig;

    /**
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Install\Model\Installer\Db\Factory $dbFactory
     * @param array $dbConfig
     */
    public function __construct(
        \Magento\Framework\Logger $logger,
        \Magento\Install\Model\Installer\Db\Factory $dbFactory,
        array $dbConfig
    ) {
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
     * @throws \Magento\Framework\Model\Exception
     */
    public function checkDbConnectionData($data)
    {
        $data = $this->_getCheckedData($data);

        try {
            /** @var \Magento\Install\Model\Installer\Db\Mysql4 $resource */
            $resource = $this->_getDbResource();
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
                throw new \Magento\Framework\Model\Exception(
                    __('PHP Extensions "%1" must be loaded.', implode(',', $absenteeExtensions))
                );
            }

            $version = $resource->getVersion();
            $requiredVersion = isset(
                $this->_dbConfig['mysql4']['min_version']
            ) ? $this->_dbConfig['mysql4']['min_version'] : 0;

            // check DB server version
            if (version_compare($version, $requiredVersion) == -1) {
                throw new \Magento\Framework\Model\Exception(
                    __(
                        'The database server version doesn\'t match system requirements (required: %1, actual: %2).',
                        $requiredVersion,
                        $version
                    )
                );
            }

            // check InnoDB support
            if (!$resource->supportEngine()) {
                throw new \Magento\Framework\Model\Exception(__('Database server does not support the InnoDB storage engine.'));
            }

            // TODO: check user roles
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->_logger->logException($e);
            throw new \Magento\Framework\Model\Exception(__($e->getMessage()));
        } catch (\Exception $e) {
            $this->_logger->logException($e);
            throw new \Magento\Framework\Model\Exception(__('Something went wrong while connecting to the database.'));
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
            throw new \Magento\Framework\Model\Exception(__('The Database Name field cannot be empty.'));
        }
        //make all table prefix to lower letter
        if ($data['db_prefix'] != '') {
            $data['db_prefix'] = strtolower($data['db_prefix']);
        }
        //check table prefix
        if ($data['db_prefix'] != '') {
            if (!preg_match('/^[a-z]+[a-z0-9_]*$/', $data['db_prefix'])) {
                throw new \Magento\Framework\Model\Exception(
                    __(
                        'The table prefix should contain only letters (a-z), numbers (0-9) or underscores (_); the first character should be a letter.'
                    )
                );
            }
        }
        //set db type according the db model
        if (!isset($data['db_type'])) {
            $data['db_type'] = isset(
                $this->_dbConfig[(string)$data['db_model']]['type']
            ) ? $this->_dbConfig[(string)$data['db_model']]['type'] : null;
        }

        $dbResource = $this->_getDbResource();
        $data['db_pdo_type'] = $dbResource->getPdoType();

        if (!isset($data['db_init_statements'])) {
            $data['db_init_statements'] = isset(
                $this->_dbConfig[(string)$data['db_model']]['initStatements']
            ) ? $this->_dbConfig[(string)$data['db_model']]['initStatements'] : null;
        }

        return $data;
    }

    /**
     * Retrieve the database resource
     *
     * @return \Magento\Install\Model\Installer\Db\AbstractDb
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _getDbResource()
    {
        if (!isset($this->_dbResource)) {
            $this->_dbResource = $this->_dbFactory->get('mysql4');
        }
        return $this->_dbResource;
    }
}
