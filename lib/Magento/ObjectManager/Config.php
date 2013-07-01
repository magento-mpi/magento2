<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_ObjectManager_Config
{
    /**
     * Interface preferences
     *
     * @var array
     */
    protected $_preferences = array();

    /**
     * Virtual types
     *
     * @var array
     */
    protected $_virtualTypes = array();

    /**
     * Instance arguments
     *
     * @var array
     */
    protected $_arguments = array();

    /**
     * Type sharebility
     *
     * @var array
     */
    protected $_nonShared = array();

    /**
     * Plugin configuration
     *
     * @var array
     */
    protected $_plugins = array();

    /**
     * Retrieve list of arguments per type
     *
     * @param string $type
     * @param array $arguments
     * @return array
     */
    public function getArguments($type, $arguments)
    {
        if (isset($this->_arguments[$type])) {
            $arguments = array_replace($this->_arguments[$type], $arguments);
        }
        return $arguments;
    }

    /**
     * Check whether type is shared
     *
     * @param string $type
     * @return bool
     */
    public function isShared($type)
    {
        return !isset($this->_nonShared[$type]);
    }

    /**
     * Retrieve instance type
     *
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
     * Retrieve preference for type
     *
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
     * Check whether type has configured plugins
     *
     * @param string $type
     * @return bool
     */
    public function hasPlugins($type)
    {
        return isset($this->_plugins[$type]);
    }

    /**
     * Retrieve list of plugins
     *
     * @param string $type
     * @return array
     */
    public function getPlugins($type)
    {
        usort($this->_plugins[$type], array($this, '_sort'));
        return $this->_plugins[$type];
    }


    /**
     * Sorting items
     *
     * @param array $itemA
     * @param array $itemB
     * @return int
     */
    protected function _sort($itemA, $itemB)
    {
        if (isset($itemA['sortOrder'])) {
            if (isset($itemB['sortOrder'])) {
                return $itemA['sortOrder'] - $itemB['sortOrder'];
            }
            return $itemA['sortOrder'];
        } else if (isset($itemB['sortOrder'])) {
            return $itemB['sortOrder'];
        } else {
            return 1;
        }
    }

    /**
     * Extend configuration
     *
     * @param array $configuration
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function extend(array $configuration)
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
                    if (isset($curConfig['plugins'])) {
                        if (isset($this->_plugins[$key])) {
                            $this->_plugins[$key] = array_replace($this->_plugins[$key], $curConfig['plugins']);
                        } else {
                            $this->_plugins[$key] = $curConfig['plugins'];
                        }
                    }
                    break;
            }
        }
    }
}
