<?php
/**
 * Magento application object manager. Configures and application application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_Local
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
     * @param Magento_Core_Model_Config_Loader_Local $loader
     */
    public function __construct(Magento_Core_Model_Config_Loader_Local $loader)
    {
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
     * Retrieve resources
     *
     * @return array
     */
    public function getResources()
    {
        return isset($this->_data['resource']) ? $this->_data['resource'] : array();
    }
}
