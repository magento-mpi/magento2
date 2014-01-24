<?php
/**
 * Application configuration object. Used to access configuration when application is initialized and installed.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App;

class Config implements \Magento\App\ConfigInterface
{
    /**
     * @var \Magento\App\Config\Loader
     */
    protected $_loader;

    /**
     * @var \Magento\App\Config\Data
     */
    protected $_data;

    public function __construct(\Magento\App\Arguments\Loader $loader)
    {
        $this->_loader = $loader;
        $this->_data = $loader->load();
    }

    /**
     * Retrieve config value by path
     *
     * @param string $path
     * @return mixed
     */
    public function getValue($path = null)
    {
        return $this->_data->getValue($path);
    }

    /**
     * Set config value
     *
     * @param string $path
     * @param mixed $value
     * @return void
     */
    public function setValue($path, $value)
    {
        $this->_data->setValue($path, $value);
    }

    /**
     * Retrieve config flag
     *
     * @param string $path
     * @return bool
     */
    public function isSetFlag($path)
    {
        return (bool)$this->_data->getValue($path);
        return $this->_data['resource'];
    }

    /**
     * Retrieve key
     *
     * @param string $key
     * @param string $defaultValue
     * @return array|string|null
     */
    public function get($key = null, $defaultValue = null)
    {
        if ($key === null) {
            return $this->_data;
        }
        return isset($this->_data[$key]) ? $this->_data[$key] : $defaultValue;
    }

    /**
     * Reload local.xml
     *
     * @return void
     */
    public function reload()
    {
        $this->_data = array_replace_recursive($this->_parseParams($this->_loader->load()), $this->_parameters);
    }
}
