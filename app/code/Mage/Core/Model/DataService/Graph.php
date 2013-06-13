<?php
/**
 * DataService graph manages creation and storage of data services.
 *
 * manages the graph of objects
 *  - initializes data service
 *  - calls factory to retrieve data
 *  - stores data to repository
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_DataService_Graph implements Mage_Core_Model_DataService_Path_NodeInterface
{
    /** @var Mage_Core_Model_DataService_Invoker */
    protected $_invoker;

    /** @var Mage_Core_Model_DataService_Repository */
    protected $_repository;

    /**
     * @param Mage_Core_Model_DataService_Invoker $dataServiceInvoker
     * @param Mage_Core_Model_DataService_Repository $repository
     */
    public function __construct(
        Mage_Core_Model_DataService_Invoker $dataServiceInvoker,
        Mage_Core_Model_DataService_Repository $repository
    ) {
        $this->_invoker = $dataServiceInvoker;
        $this->_repository = $repository;
    }

    /**
     * takes array of the following structure
     * and initializes all of the data sources
     *
     *  array(dataServiceName => array(
     *      blocks => array(
     *          'namespace' => aliasInNamespace
     *      ))
     *
     * @param array $dataServicesList
     * @return Mage_Core_Model_DataService_Graph
     * @throws Mage_Core_Exception
     */
    public function init(array $dataServicesList)
    {
        foreach ($dataServicesList as $dataServiceName => $namespaceConfig) {
            $this->get($dataServiceName);
            if (!isset($namespaceConfig['namespaces'])) {
                throw new Mage_Core_Exception("Data reference configuration doesn't have a block to link to");
            }
            foreach ($namespaceConfig['namespaces'] as $namespaceName => $aliasInNamespace) {
                $this->_repository->addNameInNamespace($namespaceName, $dataServiceName, $aliasInNamespace);
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
        $dataService = $this->_repository->get($sourceName);
        if ($dataService === null) {
            $dataService = $this->_invoker->getServiceData($sourceName);
        }
        $this->getRepository()->add($sourceName, $dataService);
        return $dataService;
    }

    /**
     * Retrieve all data for the service calls for particular namespace.
     *
     * @param $namespace
     * @return mixed
     */
    public function getByNamespace($namespace)
    {
        $dataServices = $this->getRepository()->getByNamespace($namespace);
        return $dataServices;
    }

    /**
     * Get repository object.
     *
     * @return \Mage_Core_Model_DataService_Repository
     */
    public function getRepository()
    {
        return $this->_repository;
    }

    /**
     * Return a child path node that corresponds to the input path element.  This can be used to walk the
     * data service graph.  Leaf nodes in the graph tend to be of mixed type (scalar, array, or object).
     *
     * @param string $pathElement the path element name of the child node
     * @return Mage_Core_Model_DataService_Path_Node|mixed|null the child node, or mixed if this is a leaf node
     */
    public function getChildNode($pathElement)
    {
        return $this->get($pathElement);
    }
}
