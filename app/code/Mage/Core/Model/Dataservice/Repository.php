<?php
/**
 * Data source Repository
 *
 * Stores the data and allows to retrieve for service calls.  Assigns namespaces and aliases to the service calls data.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Dataservice_Repository implements Mage_Core_Model_Dataservice_Path_NodeInterface
{
    /**
     * @var array of Mage_Core_Model_Dataservice_Path_NodeInterface
     */
    protected $_dataservices = array();

    /**
     * @var array
     */
    protected $_namespaces = array();

    /**
     * Assign a new name to existing namespace identified by alias.
     *
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
     * Get all data services from namespace.
     *
     * @param $namespace
     * @return array
     */
    public function getByNamespace($namespace)
    {
        if (!isset($this->_namespaces[$namespace])) {
            return array();
        }
        $dataservices = array();
        $dataservicesNames = $this->_namespaces[$namespace];
        foreach ($dataservicesNames as $name => $nameInNamespace) {
            $dataservices[$nameInNamespace] = $this->get($name);
        }
        return $dataservices;
    }

    /**
     * Add new service data.
     *
     * @param string $name
     * @param Mage_Core_Model_Dataservice_Path_Node $dataservice
     * @return $this
     */
    public function add($name, $dataservice)
    {
        $this->_dataservices[$name] = $dataservice;
        return $this;
    }

    /**
     * Get service data by name.
     *
     * @param string $name
     * @return Mage_Core_Model_Dataservice_Path_NodeInterface
     */
    public function get($name)
    {
        if (!isset($this->_dataservices[$name])) {
            return null;
        }
        return $this->_dataservices[$name];
    }

    /**
     * Return a child path node that corresponds to the input path element.  This can be used to walk the
     * dataservice graph.  Leaf nodes in the graph tend to be of mixed type (scalar, array, or object).
     *
     * @param string $pathElement the path element name of the child node
     * @return Mage_Core_Model_Dataservice_Path_NodeInterface|mixed|null the child node, or mixed if this is a leaf node
     */
    public function getChildNode($pathElement)
    {
        return $this->get($pathElement);
    }
}