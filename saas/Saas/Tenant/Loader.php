<?php
/**
 * Tenant information loader
 */
class Saas_Tenant_Loader
{
    /**
     * @var MongoDB
     */
    private $_db;

    /**
     * IDs in-memory cache
     *
     * @var array
     */
    private $_ids = array();

    /**
     * Data in-memory cache
     *
     * @var array
     */
    private $_data = array();

    /**
     * Inject dependency on MongoDB
     *
     * @param MongoDB $db
     */
    public function __construct(MongoDB $db)
    {
        $this->_db = $db;
    }

    /**
     * Fetch tenant ID by domain name
     *
     * @param string $domainName
     * @return bool|string
     * @throws InvalidArgumentException
     */
    public function getId($domainName)
    {
        if (isset($this->_ids[$domainName])) {
            return $this->_ids[$domainName];
        }
        if (empty($domainName) || !preg_match("/^[a-z0-9\-\.]*[a-z0-9\-]{1,63}\.[a-z]{2,}$/", $domainName)) {
            throw new InvalidArgumentException(sprintf('Incorrect domain name "%s".', $domainName));
        }
        $domain = preg_replace('/^www\./i', '', $domainName);
        $collection = $this->_db->selectCollection('tenantDomains');
        $tenant = $collection->findOne(array('domain' => str_replace('.', '*', $domain)));
        if (!$tenant) {
            $tenant = $collection->findOne(array('domain' => str_replace('.', '*', ('www.' . $domain))));
        }
        if (!$tenant || empty($tenant['tenantId'])) {
            $result = false;
        } else {
            $result = (string)$tenant['tenantId'];
        }
        $this->_ids[$domainName] = $result;
        return $result;
    }

    /**
     * Fetch all tenant data by ID
     *
     * @param string $identifier
     * @return array|bool
     */
    public function getData($identifier)
    {
        if (isset($this->_data[$identifier])) {
            return $this->_data[$identifier];
        }
        $data = $this->_db->selectCollection('tenantConfiguration')->findOne(array('tenantId' => $identifier));
        $this->_data[$identifier] = $data ?: false;
        return $this->_data[$identifier];
    }
}
