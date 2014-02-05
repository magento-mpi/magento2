<?php
/**
 * Default application path for backend area
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\App;

/**
 * Backend config accessor
 */
class Config implements ConfigInterface
{
    /**
     * @var \Magento\App\Config\ScopePool
     */
    protected $_scopePool;

    /**
     * @param \Magento\App\Config\ScopePool $scopePool
     */
    public function __construct(\Magento\App\Config\ScopePool $scopePool)
    {
        $this->_scopePool = $scopePool;
    }

    /**
     * Retrieve config value by path and scope
     *
     * @param string $path
     * @return mixed
     */
    public function getValue($path)
    {
        return $this->_scopePool->getScope('default', null)->getValue($path);
    }

    /**
     * Set config value in the corresponding config scope
     *
     * @param string $path
     * @param mixed $value
     */
    public function setValue($path, $value)
    {
        $this->_scopePool->getScope('default', null)->setValue($path, $value);
    }

    /**
     * Reinitialize configuration
     */
    public function reinit()
    {
        $this->_scopePool->clean();
    }

    /**
     * Retrieve config flag
     *
     * @param string $path
     * @return bool
     */
    public function isSetFlag($path)
    {
        return !!$this->_scopePool->getScope('default', null)->getValue($path);
    }
}
