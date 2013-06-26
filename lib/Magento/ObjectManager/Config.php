<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_ObjectManager_Config
{
    protected $_preferences = array();

    protected $_virtualTypes = array();

    protected $_arguments = array();

    protected $_nonShared = array();

    protected $_plugins = array();

    /**
     * @param string $instanceName
     * @return mixed
     */
    public function getInstanceType($instanceName)
    {
        while (isset($this->_virtualTypes[$instanceName])) {
            $instanceName = $this->_virtualTypes[$instanceName];
        }
        return $instanceName;
    }

    /**
     * @param string $type
     * @return string
     * @throws LogicException
     */
    public function getPreference($type)
    {
        $preferencePath = array();
        while (isset($this->_preferences[$type])) {
            if (isset($preferencePath[$this->_preferences[$type]])) {
                throw new LogicException(
                    'Circular type preference: ' . $type . ' relates to '
                    . $this->_preferences[$type] . ' and viceversa.'
                );
            }
            $type = $this->_preferences[$type];
            $preferencePath[$type] = 1;
        }
        return $type;
    }

    /**
     * @param string $type
     * @return bool
     */
    public function hasPlugins($type)
    {
        return isset($this->_plugins[$type]);
    }

    /**
     * @param string $type
     * @return array
     */
    public function getPluginList($type)
    {
        $sortedList = array();

    }


    /**
     * @param array $configuration
     */
    public function merge(array $configuration)
    {
        foreach ($configuration as $key => $curConfig) {
            switch ($key) {
                case 'preferences':
                    $this->_preferences = array_replace($this->_preferences, $curConfig);
                    break;

                default:
                    if (isset($curConfig['type'])) {
                        $this->_virtualTypes[$key] = $curConfig['type'];
                    }
                    if (isset($curConfig['parameters'])) {
                        if (isset($this->_arguments[$key])) {
                            $this->_arguments[$key] = array_replace($this->_arguments[$key], $curConfig['parameters']);
                        } else {
                            $this->_arguments[$key] = $curConfig['parameters'];
                        }
                    }
                    if (isset($curConfig['shared'])) {
                        if (!$curConfig['shared'] || $curConfig['shared'] == 'false') {
                            $this->_nonShared[$key] = 1;
                        } else {
                            unset($this->_nonShared[$key]);
                        }
                    }
                    if (isset($curConfig['interceptors'])) {
                        $this->_plugins[$key] = $curConfig['interceptors'];
                    }
                    break;
            }
        }
    }
}
