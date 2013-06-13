<?php
/**
 * Test class for Mage_Core_Model_DataService_Repository
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_DataService_RepositoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_DataService_Repository
     */
    protected $_repository;

    /**
     * Create repository to test
     */
    public function setup()
    {
        $this->_repository = new Mage_Core_Model_DataService_Repository();
    }

    /**
     * verify repository gets mock data service
     */
    public function testAddGet()
    {
        $dataService = (object)array();
        $name = 'name';
        $this->assertEquals($dataService, $this->_repository->add($name, $dataService)->get($name));
    }

    /**
     * verify nothing is returned if its not added
     */
    public function testGet()
    {
        $this->assertEquals(null, $this->_repository->get('name'));
    }

    /**
     * verify empty array is returned for unknown namespace
     */
    public function testGetByNamespace()
    {
        $result = $this->_repository->getByNamespace('unknown_namespace');
        $this->assertEquals(array(), $result);
    }

    /**
     * Verify dataservice is retrieved with a namespace
     */
    public function testAddGetNamespace()
    {
        $dataService = (object)array();
        $nameInNamespace = 'name_in_namespace';
        $namespace = 'namespace';
        $name = 'name';
        $namespaceResults = $this->_repository->add($name, $dataService)
            ->addNameInNamespace($namespace, $name, $nameInNamespace)
            ->getByNamespace($namespace);
        $this->assertEquals($dataService, $namespaceResults[$nameInNamespace]);
    }

    /**
     * Verify data service can be added and retrieved with different namespaces.
     */
    public function testAddGetNamespaceAgain()
    {
        $dataService = (object)array();
        $nameInNamespace = 'name_in_namespace';
        $namespace = 'namespace';
        $name = 'name';
        $namespaceResults = $this->_repository->add($name, $dataService)
            ->addNameInNamespace($namespace, $name, 'something_different')
            ->addNameInNamespace($namespace, $name, $nameInNamespace)
            ->getByNamespace($namespace);
        $this->assertEquals($dataService, $namespaceResults[$nameInNamespace]);
    }

    /**
     * Verify mock data service returned after being added.
     */
    public function testGetChild()
    {
        $dataService = (object)array();
        $name = 'name';
        $this->_repository->add($name, $dataService);
        $this->assertEquals($dataService, $this->_repository->getChildNode($name));
    }
}