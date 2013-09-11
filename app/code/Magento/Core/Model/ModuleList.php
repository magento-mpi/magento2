<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model;

class ModuleList implements \Magento\Core\Model\ModuleListInterface
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
     * @param \Magento\Core\Model\Module\Declaration\Reader\Filesystem $reader
     * @param \Magento\Config\CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        \Magento\Core\Model\Module\Declaration\Reader\Filesystem $reader,
        \Magento\Config\CacheInterface $cache,
        $cacheId = 'modules_declaration_cache'
    ) {
        $data = $cache->get($this->_scope, $cacheId);
        if (!$data) {
            $data = $reader->read($this->_scope);
            $cache->put($data, $this->_scope, $cacheId);
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
