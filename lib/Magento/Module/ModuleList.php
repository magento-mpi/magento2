<?php
/**
 * List of application active application modules.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Module;

use Magento\Config\CacheInterface;

class ModuleList implements \Magento\Module\ModuleListInterface
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
     * @param Declaration\Reader\Filesystem $reader
     * @param CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        Declaration\Reader\Filesystem $reader,
        CacheInterface $cache,
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
