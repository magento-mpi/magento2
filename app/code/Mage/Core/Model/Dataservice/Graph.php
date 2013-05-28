<?php
/**
 * Dataservice graph manages creation and storage of dataservices.
 *
 * manages the graph of objects
 *  - initializes datasource
 *  - calls factory to retrieve data
 *  - stores data to repository
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Dataservice_Graph implements Mage_Core_Model_Dataservice_Path_Node
{
    /** @var Mage_Core_Model_Dataservice_Factory */
    protected $_factory;

    /** @var Mage_Core_Model_Dataservice_Repository */
    protected $_repository;

    /**
     * @param Mage_Core_Model_Dataservice_Factory
     * @param Mage_Core_Model_Dataservice_Repository $repository
     */
    public function __construct(
        Mage_Core_Model_Dataservice_Factory $factory,
        Mage_Core_Model_Dataservice_Repository $repository
    ) {
        $this->_factory = $factory;
        $this->_repository = $repository;
    }

    /**
     * Get the value for the method argument
     *
     * @param $path
     * @return null
     */
    public function getArgumentValue($path)
    {
        return $this->_factory->getArgumentValue($path);
    }

    /**
     * takes array of the following structure
     * and initializes all of the data sources
     *
     *  array(dataserviceName => array(
     *      blocks => array(
     *          'namespace' => aliasInNamespace
     *      ))
     *
     * @param array $dataservicesList
     * @return Mage_Core_Model_Dataservice_Graph
     * @throws Mage_Core_Exception
     */
    public function init(array $dataservicesList)
    {
        foreach ($dataservicesList as $dataserviceName => $namespaceConfig) {
            $this->get($dataserviceName);
            if (!isset($namespaceConfig['namespaces'])) {
                throw new Mage_Core_Exception("Data reference configuration doesn't have a block to link to");
            }
            foreach ($namespaceConfig['namespaces'] as $namespaceName => $aliasInNamespace) {
                $this->_repository->addNameInNamespace($namespaceName, $dataserviceName, $aliasInNamespace);
            }
        }
        return $this;
    }

    /**
     * Retrieve the data or the service call based on its name
     *
     * @param $sourceName
     * @return bool|mixed
     */
    public function get($sourceName)
    {
        $dataservice = $this->_repository->get($sourceName);
        if ($dataservice == null) {
            $dataservice = $this->_factory->createDataservice($sourceName);
        }
        $this->getRepository()->add($sourceName, $dataservice);
        return $dataservice;
    }

    /**
     * Retrieve all data for the service calls for particular namespace.
     *
     * @param $namespace
     * @return mixed
     */
    public function getByNamespace($namespace)
    {
        $dataservices = $this->getRepository()->getByNamespace($namespace);
        return $dataservices;
    }

    /**
     * Get repository object.
     *
     * @return \Mage_Core_Model_Dataservice_Repository
     */
    public function getRepository()
    {
        return $this->_repository;
    }

    /**
     * Return a child path node that corresponds to the input path element.  This can be used to walk the
     * dataservice graph.  Leaf nodes in the graph tend to be of mixed type (scalar, array, or object).
     *
     * @param string $pathElement the path element name of the child node
     * @return Mage_Core_Model_Dataservice_Path_Node|mixed|null the child node, or mixed if this is a leaf node
     */
    public function getChild($pathElement)
    {
        return $this->get($pathElement);
    }
}
