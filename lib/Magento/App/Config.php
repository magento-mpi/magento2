<?php
/**
 * Application deployment configuration that contain settings, values of which may vary from one installation to another
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

class Config
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
     * @var \Magento\App\Config\Loader
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
     * @param Config\Loader $loader
     */
    public function __construct(array $parameters, Config\Loader $loader)
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
        unset($stack['cache']);
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
        $output['cache'] = isset($input['cache']) ? $input['cache'] : array();
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
        return isset($this->_data['cache']['type'][$cacheType]['frontend'])
            ? $this->_data['cache']['type'][$cacheType]['frontend']
            : null;
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
