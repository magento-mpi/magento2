<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Backend Acl Config model
 *
 * @category    Mage
 * @package     Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Acl_Config
{
    const CACHE_ID = 'backend_acl_resources';

    const ACL_RESOURCE_ALL = 'Mage_Adminhtml::all';

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @var Mage_Core_Model_Cache
     */
    protected $_cache;

    /**
     * @var Mage_Backend_Model_Acl_Config_Reader
     */
    protected $_reader;

    public function __construct(array $args = array())
    {
        $this->_config = isset($args['config']) ? $args['config'] : Mage::getConfig();
        $this->_cache  = isset($args['cache']) ? $args['cache'] : Mage::app()->getCacheInstance();
    }

    /**
     * Retrieve list of acl files from each module
     *
     * @return array
     */
    protected function _getAclResourceFiles()
    {
        $files = $this->_config
            ->getModuleConfigurationFiles('adminhtml' . DIRECTORY_SEPARATOR . 'acl.xml');
        return (array) $files;
    }

    /**
     * Reader object initialization
     *
     * @return Mage_Backend_Model_Acl_Config_Reader
     */
    protected function _getReader()
    {
        if (null === $this->_reader) {
            $aclResourceFiles = $this->_getAclResourceFiles();
            $this->_reader = $this->_config->getModelInstance('Mage_Backend_Model_Acl_Config_Reader', $aclResourceFiles);
        }
        return $this->_reader;
    }

    /**
     * Return ACL Resources loaded from cache if enabled or from files merged previously
     *
     * @return DOMDocument
     */
    public function getAclResources()
    {
        $aclResourcesXml = $this->_loadAclResourcesFromCache();
        if ($aclResourcesXml && is_string($aclResourcesXml)) {
            $aclResources = new DOMDocument();
            $aclResources->loadXML($aclResourcesXml);
        } else {
            $aclResources = $this->_getReader()->getAclResources();
            $this->_saveAclResourcesToCache($aclResources->saveXML());
        }
        $xpath = new DOMXPath($aclResources);
        return $xpath->query('/config/acl/resources/*');
    }

    /**
     * Load ACL resources from cache
     *
     * @return null|string
     */
    private function _loadAclResourcesFromCache()
    {
        if ($this->_cache->canUse('config')) {
            return $this->_cache->load(self::CACHE_ID);
        }
        return null;
    }

    /**
     * Save ACL resources into the cache
     *
     * @param $data
     * @return Mage_Backend_Model_Acl_Config
     */
    private function _saveAclResourcesToCache($data)
    {
        if ($this->_cache->canUse('config')) {
            $this->_cache->save($data, self::CACHE_ID, array(Mage_Core_Model_Config::CACHE_TAG));
        }
        return $this;
    }
}
