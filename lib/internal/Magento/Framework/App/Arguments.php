<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App;

use Magento\Framework\App\DeploymentConfig\DbConfig;
use Magento\Framework\App\DeploymentConfig\ResourceConfig;

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
     * @var \Magento\Framework\App\Arguments\Loader
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
     * @param \Magento\Framework\App\Arguments\Loader $loader
     */
    public function __construct(array $parameters, \Magento\Framework\App\Arguments\Loader $loader)
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
        $output = array();
        $output['connection'] = isset($input[DbConfig::CONFIG_KEY]['connection']) ?
            $input[DbConfig::CONFIG_KEY]['connection'] : array();
        $output['resource'] = isset($input[ResourceConfig::CONFIG_KEY]) ? $input[ResourceConfig::CONFIG_KEY] : array();
        $output['cache'] = isset($input['cache']) ? $input['cache'] : array();
        unset($input[DbConfig::CONFIG_KEY]['connection']);
        unset($input[ResourceConfig::CONFIG_KEY]);
        unset($input['cache']);

        $output = array_merge($output, $this->_flattenParams($input));
        return $output;
    }

    /**
     * Convert associative array of arbitrary depth to a flat associative array with concatenated key path as keys
     *
     * @param array $params
     * @param string $separator
     * @return array
     */
    protected function _flattenParams(array $params, $separator = '.')
    {
        $result = array();
        $stack = $params;
        while ($stack) {
            list($key, $value) = each($stack);
            unset($stack[$key]);
            if (is_array($value)) {
                if (count($value)) {
                    foreach ($value as $subKey => $node) {
                        $build[$key . $separator . $subKey] = $node;
                    }
                    if (array_key_exists($key, $build)) {
                        unset($build[$key]);
                    }
                } else {
                    $build[$key] = null;
                }
                $stack = $build + $stack;
                continue;
            }
            $result[$key] = $value;
        }
        return $result;
    }

    /**
     * Retrieve connection configuration by connection name
     *
     * @param string $connectionName
     * @return array
     */
    public function getConnection($connectionName)
    {
        return isset($this->_data['connection'][$connectionName]) ? $this->_data['connection'][$connectionName] : null;
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
        return isset($this->_data['resource']) ? $this->_data['resource'] : array();
    }

    /**
     * Retrieve settings for all cache front-ends configured in the system
     *
     * @return array Format: array('<frontend_id>' => array(<cache_settings>), ...)
     */
    public function getCacheFrontendSettings()
    {
        return isset($this->_data['cache']['frontend']) ? $this->_data['cache']['frontend'] : array();
    }

    /**
     * Retrieve identifier of a cache frontend, configured to be used for a cache type
     *
     * @param string $cacheType Cache type identifier
     * @return string|null
     */
    public function getCacheTypeFrontendId($cacheType)
    {
        return isset(
            $this->_data['cache']['type'][$cacheType]['frontend']
        ) ? $this->_data['cache']['type'][$cacheType]['frontend'] : null;
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
     * Reload config.php
     *
     * @return void
     */
    public function reload()
    {
        $this->_data = array_replace_recursive($this->_parseParams($this->_loader->load()), $this->_parameters);
    }
}
