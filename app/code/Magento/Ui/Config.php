<?php
/**
 * UI Library configuration settings.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui;

use Magento\Framework\Config\CacheInterface;
use Magento\Ui\Config\Reader;
use Magento\Framework\Object;

class Config extends Object implements ConfigInterface
{
    /**
     * Configuration scope
     *
     * @var string
     */
    protected $_scope = 'global';

    /**
     * @param Reader $reader
     * @param CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(Reader $reader, CacheInterface $cache, $cacheId = 'ui_library_cache')
    {
        $data = $cache->load($this->_scope . '::' . $cacheId);
        if (!$data) {
            $data = $reader->read($this->_scope);
            $cache->save(serialize($data), $this->_scope . '::' . $cacheId);
        } else {
            $data = unserialize($data);
        }
        parent::__construct($data);
    }

    /**
     * Get configuration value by path
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function getValue($key = '', $default = null)
    {
        $result = $this->getData($key);
        return $result === null ? $default : $result;
    }
}
