<?php
/**
 * Magento application object manager. Configures and application application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config;

class Local
{
    /**
     * Config data
     *
     * @var array
     */
    protected $_data;

    /**
     * DI configuration
     *
     * @var array
     */
    protected $_configuration = array();

    /**
     * @var \Magento\Core\Model\Config\Loader\Local
     */
    protected $_loader;

    /**
     * @param \Magento\Core\Model\Config\Loader\Local $loader
     */
    public function __construct(\Magento\Core\Model\Config\Loader\Local $loader)
    {
        $this->_loader = $loader;
        $this->_data = $loader->load();
    }

    /**
     * @return array
     */
    public function getParams()
    {
        $stack = $this->_data;
        unset($stack['resource']);
        unset($stack['connection']);
        $separator = '.';
        $parameters = array();

        while ($stack) {
            list($key, $value) = each($stack);
            unset($stack[$key]);
            if (is_array($value)) {
                if (count($value)) {
                    foreach ($value as $subKey => $node) {
                        $build[$key . $separator . $subKey] = $node;
                    }
                } else {
                    $build[$key] = null;
                }
                $stack = $build + $stack;
                continue;
            }
            $parameters[$key] = $value;
        }
        return $parameters;
    }

    /**
     * Retrieve connection configuration by connection name
     *
     * @param string $connectionName
     * @return array
     */
    public function getConnection($connectionName)
    {
        return isset($this->_data['connection'][$connectionName])
            ? $this->_data['connection'][$connectionName]
            : null;
    }

    /**
     * Retrieve list of connections
     *
     * @return array
     */
    public function getConnections()
    {
        return isset($this->_data['connection']) ? $this->_data['connection'] : array();
    }

    /**
     * Retrieve resources
     *
     * @return array
     */
    public function getResources()
    {
        return isset($this->_data['resource']) ? $this->_data['resource'] : array();
    }

    /**
     * Reload local.xml
     */
    public function reload()
    {
        $this->_data = $this->_loader->load();
    }
}
