<?php
/**
 * Magento application object manager. Configures and application application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

class Arguments
{
    /**
     * Config data
     *
     * @var array
     */
    protected $_data;

    /**
     * Configuration loader
     *
     * @var \Magento\App\Arguments\Loader
     */
    protected $_loader;

    /**
     * Application options
     *
     * @var array
     */
    protected $_parameters;

    /**
     * @param array $parameters
     * @param \Magento\App\Arguments\Loader $loader
     */
    public function __construct(array $parameters, \Magento\App\Arguments\Loader $loader)
    {
        $this->_loader = $loader;
        $this->_parameters = $parameters;
        $this->_data = array_replace_recursive($this->_parseParams($loader->load()), $parameters);
    }

    /**
     * @param array $input
     * @return array
     */
    protected function _parseParams(array $input)
    {
        $stack = $input;
        unset($stack['resource']);
        unset($stack['connection']);
        $separator = '.';
        $output = array();

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
            $output[$key] = $value;
        }
        $output['connection'] = isset($input['connection']) ? $input['connection'] : array();
        $output['resource'] = isset($input['resource']) ? $input['resource'] : array();
        return $output;
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
     * Retrieve list of resources
     *
     * @return array
     */
    public function getResources()
    {
        return $this->_data['resource'];
    }

    /**
     * Retrieve key
     *
     * @param string $key
     * @param mixed $defaultValue
     * @return array|null
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
     */
    public function reload()
    {
        $this->_data = array_replace_recursive($this->_parseParams($this->_loader->load()), $this->_parameters);
    }
}
