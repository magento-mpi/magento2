<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Core\Model\ObjectManager;

class ConfigCache implements \Magento\ObjectManager\ConfigCache
{
    /**
     * @var \Magento\Cache\FrontendInterface
     */
    protected $_cacheFrontend;

    /**
     * Cache prefix
     *
     * @var string
     */
    protected $_prefix = 'diConfig';

    /**
     * @param \Magento\Cache\FrontendInterface $cacheFrontend
     */
    public function __construct(\Magento\Cache\FrontendInterface $cacheFrontend)
    {
        $this->_cacheFrontend = $cacheFrontend;
    }

    /**
     * Retrieve configuration from cache
     *
     * @param string $key
     * @return array
     */
    public function get($key)
    {
        return unserialize($this->_cacheFrontend->load($this->_prefix . $key));
    }

    /**
     * Save config to cache
     *
     * @param array $config
     * @param string $key
     */
    public function save(array $config, $key)
    {
        $this->_cacheFrontend->save(serialize($config), $this->_prefix . $key);
    }

}
