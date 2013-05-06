<?php
/**
 * Test class for Mage_Core_Model_Dataservice_Repository
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Dataservice_RepositoryTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Core_Model_Dataservice_Repository */
    protected $_repository;

    public function setup()
    {
        $this->_repository = new Mage_Core_Model_Dataservice_Repository();
    }

    public function testAddGet()
    {
        $dataService = (object)array();
        $name = 'name';
        $this->assertEquals($dataService, $this->_repository->add($name, $dataService)->get($name));
    }

    public function testAddGetNamespace()
    {
        $dataService = (object)array();
        $nameInNamespace = 'name_in_namespace';
        $namespace = 'namespace';
        $name = 'name';
        $namespaceResults = $this->_repository->add($name, $dataService)->addNameInNamespace(
            $namespace, $name, $nameInNamespace
        )->getByNamespace($namespace);
        $this->assertEquals($dataService, $namespaceResults[$nameInNamespace]);
    }

    public function testVisit()
    {
        $dataService = (object)array();
        $name = 'name';
        $this->_repository->add($name, $dataService);
        $visitor = $this->getMock('Mage_Core_Model_Dataservice_Path_Visitor', array(), array(), "", false);
        $visitor->expects($this->once())->method('getCurrentPathElement')->will($this->returnValue($name));
        $this->assertEquals($dataService, $this->_repository->visit($visitor));
    }
}