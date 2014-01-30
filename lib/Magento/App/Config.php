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

    /**
     * @param Arguments\Loader $loader
     */
    public function __construct(Arguments\Loader $loader)
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
    }
}
