<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Model\Resource\Entity;

abstract class AbstractEntity
{
    /**
     * @var string
     */
    protected $_name = null;

    /**
     * Configuration object
     *
     * @var \Magento\Simplexml\Config
     */
    protected $_config = array();

    /**
     * Set config
     *
     * @param \Magento\Simplexml\Config $config
     */
    public function __construct($config)
    {
        $this->_config = $config;
    }

    /**
     * Get config by key
     *
     * @param string $key
     * @return \Magento\Simplexml\Config|string|false
     */
    public function getConfig($key = '')
    {
        if ('' === $key) {
            return $this->_config;
        } elseif (isset($this->_config->{$key})) {
            return $this->_config->{$key};
        } else {
            return false;
        }
    }
}
