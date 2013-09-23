<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_ModuleList implements Magento_Core_Model_ModuleListInterface
{
    /**
     * Configuration data
     *
     * @var array
     */
    protected $_data;

    /**
     * Configuration scope
     *
     * @var string
     */
    protected $_scope = 'global';

    /**
     * @param Magento_Core_Model_Module_Declaration_Reader_Filesystem $reader
     * @param Magento_Config_CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        Magento_Core_Model_Module_Declaration_Reader_Filesystem $reader,
        Magento_Config_CacheInterface $cache,
        $cacheId = 'modules_declaration_cache'
    ) {
        $data = $cache->load($this->_scope . '::' .  $cacheId);
        if (!$data) {
            $data = $reader->read($this->_scope);
            $cache->save(serialize($data), $this->_scope . '::' . $cacheId);
        } else {
            $data = unserialize($data);
        }
        $this->_data = $data;
    }

    /**
     * Get configuration of all declared active modules
     *
     * @return array
     */
    public function getModules()
    {
        return $this->_data;
    }

    /**
     * Get module configuration
     *
     * @param string $moduleName
     * @return array|null
     */
    public function getModule($moduleName)
    {
        return isset($this->_data[$moduleName]) ? $this->_data[$moduleName] : null;
    }
}
