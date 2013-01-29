<?php
/**
 * @deprecated after 2.0.0.0-dev39
 */
class Saas_Tenant
{
    protected $_id;
    protected $_config;
    protected $_localXml;

    /**
     * Tenant constructor
     *
     * @param int $id
     * @param array $config
     */
    public function __construct($id, $config)
    {
        $this->_id = $id;
        $this->_config = $config;
    }

    /**
     * Retrive type of tenant database: custom dump, sample data, empty data.
     *
     * @return string
     */
    public function getDbType()
    {
        return !empty($this->_config['custom_db']) ? 'cust' : (empty($this->_config['sample_data']) ? 'empt' : 'smpl');
    }

    public function getId()
    {
        return $this->_id;
    }

    /**
     * Return local.xml contens as XML object
     *
     * @return SimpleXmlElement
     */
    public function getLocalXml()
    {
        if (is_null($this->_localXml)) {
            $this->_localXml = new SimpleXmlElement($this->_config['tenantConfiguration']['local']);
        }
        return $this->_localXml;
    }

    /**
     * Return media dir path from local.xml
     *
     * @return string
     */
    public function getMediaDir()
    {
        $xml = $this->getLocalXml();
        if (empty($xml->global->web->dir->media)) {
            $mediaDir = $this->getId();
        } else {
            $mediaDir = (string)$xml->global->web->dir->media;
        }
        return 'media' . DIRECTORY_SEPARATOR . $mediaDir;
    }

    /**
     * Build base dir of the application based on current tenant's version
     *
     * @param string $codeBasesDir
     * @return string
     * @throws Exception If there is no way to
     */
    public function getMagentoDir()
    {
        return saasMagentoDir($this->getVersion());
    }

    /**
     * Version getter
     *
     * string
     */
    public function getVersion()
    {
        if (isset($this->_config['version'])) {
            return $this->_config['version'];
        }
        throw new Exception(sprintf('Unable to determine tenant "%s" version.', $this->_id));
    }

    /**
     * Hashed version getter.
     *
     * @return int
     */
    public function getVersionHash()
    {
        static $hash = null;

        if (null === $hash) {
            $hash = sprintf('%u', crc32(md5($this->getVersion())));
        }
        return $hash;
    }

    /**
     * Check if tenant is currently under maintenance mode
     *
     * @return bool
     */
    public function isUnderMaintenance()
    {
        return !empty($this->_config['maintenanceMode']);
    }

    /**
     * Region getter
     *
     * @return string|null
     */
    public function getRegion()
    {
        if (isset($this->_config['region'])) {
            return $this->_config['region'];
        } else {
            return null; //for backward compatibility
        }
    }

    /**
     * Maintenence mode getter
     *
     * @return string|null
     */
    public function getMaintenanceMode()
    {
        if (isset($this->_config['maintenance_mode'])) {
            return $this->_config['maintenance_mode'];
        } else {
            return null; //for backward compatibility
        }
    }
}
