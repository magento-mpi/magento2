<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Magento_Core_Model_ObjectManager_ConfigCache implements Magento_ObjectManager_ConfigCache
{
    /**
     * @var Magento_Cache_FrontendInterface
     */
    protected $_cacheFrontend;

    /**
     * Cache prefix
     *
     * @var string
     */
    protected $_prefix = 'diConfig';

    /**
     * @param Magento_Cache_FrontendInterface $cacheFrontend
     */
    public function __construct(Magento_Cache_FrontendInterface $cacheFrontend)
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
