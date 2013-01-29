<?php
/**
 * Tenant code base domain model
 */
class Saas_Tenant_CodeBase
{
    /**
     * Base directory where all code bases per version are deployed
     *
     * @var string
     */
    private $_baseDir;

    /**
     * @var string
     */
    private $_id;

    /**
     * @var string
     */
    private $_version;

    /**
     * @var bool
     */
    private $_isMaintenance;

    /**
     * @var SimpleXmlElement
     */
    private $_localXml;

    /**
     * Instantiate and initialize state properly
     *
     * @param string $identifier
     * @param string $baseDir
     * @param array $config
     * @throws LogicException
     */
    public function __construct($identifier, $baseDir, array $config)
    {
        $this->_id = $identifier;
        if (!is_dir($baseDir)) {
            throw new LogicException("Directory does not exist: '{$baseDir}'");
        }
        $this->_baseDir = $baseDir;
        $data = $this->_extractArrayElement($config, 'tenantConfiguration');
        $this->_localXml = new SimpleXmlElement($this->_extractArrayElement($data, 'local'));
        $this->_version = (string)$this->_extractArrayElement($config, 'version');
        $this->_isMaintenance = !empty($config['maintenanceMode']);
    }

    /**
     * Assert that an element exists in array and return it
     *
     * @param array $array
     * @param string $key
     * @return mixed
     * @throws InvalidArgumentException
     */
    private function _extractArrayElement(array $array, $key)
    {
        if (!array_key_exists($key, $array)) {
            throw new InvalidArgumentException("Missing key '{$key}'");
        }
        return $array[$key];
    }

    /**
     * Get ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Get version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->_version;
    }

    /**
     * Get is under maintenance
     *
     * @return bool
     */
    public function isUnderMaintenance()
    {
        return $this->_isMaintenance;
    }

    /**
     * Get code base directory depending on version
     *
     * @return string
     * @throws LogicException
     */
    public function getDir()
    {
        $dir = $this->_baseDir . DIRECTORY_SEPARATOR . $this->_version;
        if (!is_dir($dir)) {
            throw new LogicException("Directory does not exist: '{$dir}'");
        }
        return $dir;
    }

    /**
     * Get custom name of media directory
     *
     * @return string
     */
    public function getMediaDirName()
    {
        if (empty($this->_localXml->global->web->dir->media)) {
            $mediaDir = $this->getId();
        } else {
            $mediaDir = (string)$this->_localXml->global->web->dir->media;
        }
        return 'media' . DIRECTORY_SEPARATOR . $mediaDir;
    }
}
