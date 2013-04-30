<?php
/**
 * Data source Repository
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Dataservice_Repository implements Mage_Core_Model_Dataservice_Path_Visitable
{
    /**
     * @var array
     */
    protected $_dataServices = array();

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
        $dataServices = array();
        $dataServicesNames = $this->_namespaces[$namespace];
        foreach ($dataServicesNames as $name => $nameInNamespace) {
            $dataServices[$nameInNamespace] = $this->get($name);
        }
        return $dataServices;
    }

    /**
     * @param $name
     * @param $dataService
     * @return $this
     */
    public function add($name, $dataService)
    {
        $this->_dataServices[$name] = $dataService;
        return $this;
    }

    /**
     * @param $name
     * @return null
     */
    public function get($name)
    {
        if (!isset($this->_dataServices[$name])) {
            return null;
        }
        return $this->_dataServices[$name];
    }

    /**
     * Make the Dataservice Object visitable
     *
     * @param Mage_Core_Model_Dataservice_Path_Visitor $visitor
     * @return bool|mixed
     */
    public function visit(Mage_Core_Model_Dataservice_Path_Visitor $visitor)
    {
        $sourceName = $visitor->getCurrentPathElement();
        $dataService = $this->get($sourceName);
        if ($dataService == null) {
            // TODO: What about null values?
        }
        return $dataService;
    }
}