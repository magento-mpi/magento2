<?php
/**
 * Data source Repository
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Datasource_Repository
{
    /**
     * @var array
     */
    protected $_dataSources = array();

    /**
     * @var array
     */
    protected $_namespaces = array();

    /**
     * @param $namespace
     * @param $name
     * @param $nameInNamespace
     * @return $this
     */
    public function addNameInNamespace($namespace, $name, $nameInNamespace)
    {
        if (isset($this->_namespaces[$namespace])) {
            $this->_namespaces[$namespace][$name] = $nameInNamespace;
        } else {
            $this->_namespaces[$namespace] = array($name => $nameInNamespace);
        }
        return $this;
    }

    /**
     * @param $namespace
     * @return array
     */
    public function getByNamespace($namespace)
    {
        if (!isset($this->_namespaces[$namespace])) {
            return array();
        }
        $dataSources = array();
        $dataSourcesNames = $this->_namespaces[$namespace];
        foreach ($dataSourcesNames as $name => $nameInNamespace) {
            $dataSources[$nameInNamespace] = $this->get($name);
        }
        return $dataSources;
    }

    /**
     * @param $name
     * @param $dataSource
     * @return $this
     */
    public function add($name, $dataSource)
    {
        $this->_dataSources[$name] = $dataSource;
        return $this;
    }

    /**
     * @param $name
     * @return null
     */
    public function get($name)
    {
        if (!isset($this->_dataSources[$name])) {
            return null;
        }
        return $this->_dataSources[$name];
    }
}