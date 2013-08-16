<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Magento_Interception_PluginList_PluginList implements Magento_Interception_PluginList
{
    /**
     * Retrieve list of plugins listening for method
     *
     * @param string $type
     * @param string $method
     * @param string $scenario
     * @return array
     */
    public function getPlugins($type, $method, $scenario)
    {
        $this->_loadScopedData();
        if (!isset($this->_data['processed'][$this->_omConfig->getInstanceType($type)][$method][$scenario])) {
            $this->_inheritPlugins($type);
        }
        return $this->_data['processed'][$this->_omConfig->getInstanceType($type)][$method][$scenario];
    }



    /**
     * Collect parent types configuration for requested type
     *
     * @param string $type
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _inheritPlugins($type)
    {
        if (!isset($this->_data['inherited'][$type])) {
            $realType = $this->_omConfig->getInstanceType($type);
            if ($realType !== $type) {
                $plugins = $this->_inheritPlugins($realType);
            } else if ($this->_relations->has($type)) {
                $relations = $this->_relations->getParents($type);
                $plugins = array();
                foreach ($relations as $relation) {
                    if ($relation) {
                        $relationPlugins = $this->_inheritPlugins($relation);
                        if ($relationPlugins) {
                            $plugins = array_replace_recursive($plugins, $relationPlugins);
                        }
                    }
                }
            } else {
                $plugins = array();
            }

            if (isset($this->_data[$type])) {
                if (!$plugins) {
                    $plugins = $this->_data[$type];
                } else {
                    $plugins = array_replace_recursive($plugins, $this->_data[$type]);
                }
            }
            usort($plugins, array($this, '_sort'));
            $this->_data['inherited'][$type] = $plugins;
            foreach ($plugins as $plugin) {
                foreach ($this->_definitions->getMethodList($plugin) as $method) {
                    foreach ($method as $scenario) {
                        $this->_data['processed'][$type][$method][$scenario][] = $plugin;
                    }
                }
            }
            return $plugins;
        }
        return $this->_data['inherited'][$type];
    }

    /**
     * Sort items
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


    protected function _loadScopedData()
    {
        parent::_loadScopedData();
    }

    public function merge(array $config)
    {
        parent::merge($config);
        if ($this->_classDefinitions) {
            foreach ($this->_classDefinitions->getClasses() as $class) {
            }
        }
    }

}
