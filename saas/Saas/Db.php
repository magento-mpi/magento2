<?php
/**
 * Tenant reader class
 */
class Saas_Db
{
    const COLLECTION_DOMAIN = 'tenantDomains';
    const COLLECTION_CONFIG = 'tenantConfiguration';

    /**
     * @var \MongoDB
     */
    protected $_db = null;

    /**
     * @var \Mongo
     */
    protected $_mongo = null;

    /**
     * @var \Saas_Tenant_Loader
     */
    protected $_loader;

    /**
     * @var \Saas_Db
     */
    protected static $_instance = null;

    private $_persist = false;

    /**
     * Create the singleton instance
     *
     * @param string $dsn
     * @param string $dbName
     * @param array $options
     * @throws \LogicException
     */
    public static function init($dsn, $dbName, array $options)
    {
        if (self::$_instance) {
            throw new LogicException('Singleton is already initialized.');
        }
        self::$_instance = new Saas_Db($dsn, $dbName, $options);
    }

    /**
     * Get the singleton instance
     *
     * @return \Saas_Db
     * @throws \LogicException
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            throw new LogicException('There is no instance set.');
        }
        return self::$_instance;
    }

    /**
     * Initialize DB connection
     */
    private function __construct($dsn, $dbName, array $options)
    {
        if (isset($options['persist'])) {
            $this->_persist = (bool)$options['persist'];
        }
        $this->_mongo = new Mongo($dsn, $options);
        $this->_db = $this->_mongo->selectDB($dbName);
        $this->_loader = new Saas_Tenant_Loader($this->_db);
    }

    /**
     * Get the selected DB
     *
     * @return MongoDB
     */
    public function getDb()
    {
        return $this->_db;
    }

    /**
     * Disconnect
     */
    public function __destruct()
    {
        if (!$this->_persist) {
            $this->_mongo->close();
        }
    }

    /**
     * Instantiate code base object by specified domain name and base directory of the deployment
     *
     * @param string $domainName
     * @param string $deploymentDir
     * @return \Saas_Tenant_CodeBase
     * @throws \Saas_Db_WrongTenantException
     * @throws \Saas_Db_WrongTenantException
     */
    public function getTenantCodeBase($domainName, $deploymentDir)
    {
        try {
            $identifier = $this->_loader->getId($domainName);
            if (!$identifier) {
                throw new Exception("Unable to load a tenant by domain name '{$domainName}'.");
            }
            return new Saas_Tenant_CodeBase($identifier, $deploymentDir, $this->_loader->getData($identifier));
        } catch (Exception $e) {
            throw new Saas_Db_WrongTenantException($e->getMessage(), 0, $e);
        }
    }

    /**
     * Initialize tenant by domain name
     *
     * @param string $domainName
     * @return \Saas_Tenant
     * @throws \Saas_Db_WrongTenantException
     * @deprecated after 2.0.0.0-dev39
     */
    public function getTenantByDomain($domainName)
    {
        try {
            $identifier = $this->_loader->getId($domainName);
        } catch (InvalidArgumentException $e) {
            throw new Saas_Db_WrongTenantException($e->getMessage(), 0, $e);
        }
        if (!$identifier) {
            throw new Saas_Db_WrongTenantException("Unable to load a tenant by domain name '{$domainName}'.");
        }
        return $this->getTenantById($identifier);
    }

    /**
     * Initialize tenant by tenant ID
     *
     * @param string $identifier
     * @return \Saas_Tenant
     * @throws \Saas_Db_WrongTenantException
     * @deprecated after 2.0.0.0-dev39
     */
    public function getTenantById($identifier)
    {
        return new Saas_Tenant($identifier, $this->getTenantData($identifier));
    }

    /**
     * @param $identifier
     * @return array
     * @throws \Saas_Db_WrongTenantException
     */
    public function getTenantData($identifier)
    {
        $data = $this->_loader->getData((string)$identifier);
        if (!$data) {
            throw new Saas_Db_WrongTenantException("Unable to load tenant '{$identifier}' configuration.");
        }
        return $data;
    }

    /**
     * Return iterator that will allow to go over all tenant ids
     *
     * @return Traversable
     */
    public function getActiveTenantsIterator()
    {
        $collection = $this->_db->selectCollection(self::COLLECTION_CONFIG);
        return $collection->find(array(), array('tenantId', 'status'));
    }

    /**
     * Convert symbols . to *
     *
     * @param string $str
     * @return string
     * @deprecated after 2.0.0.0-dev39
     */
    public static function strToMongo($str)
    {
        return str_replace('.', '*', $str);
    }

    /**
     * Convert symbols * to .
     *
     * @param string $str
     * @return string
     * @deprecated after 2.0.0.0-dev39
     */
    public static function strFromMongo($str)
    {
        return str_replace('*', '.', $str);
    }
}
