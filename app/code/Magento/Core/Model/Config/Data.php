<?php
/**
 * Configuration data container for default, stores and websites config values
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config;

class Data implements \Magento\Core\Model\Config\DataInterface
{
    /**
     * Config data
     *
     * @var array
     */
    protected $_data = array();

    /**
     * @param \Magento\Core\Model\Config\MetadataProcessor $processor
     * @param array $data
     */
    public function __construct(\Magento\Core\Model\Config\MetadataProcessor $processor, array $data)
    {
        $this->_data = $processor->process($data);
    }

    /**
     * Retrieve configuration value by path
     *
     * @param null|string $path
     * @return array|string
     */
    public function getValue($path = null)
    {
        if ($path === null) {
            return $this->_data;
        }
        $keys = explode('/', $path);
        $data = $this->_data;
        foreach ($keys as $key) {
            if (is_array($data) && array_key_exists($key, $data)) {
                $data = $data[$key];
            } else {
                return false;
            }
        }
        return $data;
    }

    /**
     * Set configuration value
     *l
     * @param string $path
     * @param mixed $value
     */
    public function setValue($path, $value)
    {
        $keys = explode('/', $path);
        $lastKey = array_pop($keys);
        $currentElement = &$this->_data;
        foreach ($keys as $key) {
            if (!isset($currentElement[$key])) {
                $currentElement[$key] = array();
            }
            $currentElement = &$currentElement[$key];
        }
        $currentElement[$lastKey] = $value;
    }
}
