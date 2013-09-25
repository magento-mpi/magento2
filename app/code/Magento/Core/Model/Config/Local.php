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
        if (isset($this->_data['resource'])) {
            if (isset($this->_data['resource']['name'])) {
                $this->_data['resource'] = array($this->_data['resource']);
            }
            foreach ($this->_data['resource'] as $resourceVal) {
                $resourceConfig = array(
                    'type' => isset($resourceVal['extend']) ? $resourceVal['extend']
                        : 'Magento_Core_Model_Resource_Type_Db_Pdo_Mysql',
                    'parameters' => $resourceVal['connection']
                );
                $this->_configuration[$resourceVal['name']] = $resourceConfig;
            }
        }
        unset($this->_data['resource']);
    }

    /**
     * @return array
     */
    public function getParams()
    {
        unset($this->_data['resource']);
        $stack = $this->_data;
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
     * @return array
     */
    public function getConfiguration()
    {
        return $this->_configuration;
    }
}
