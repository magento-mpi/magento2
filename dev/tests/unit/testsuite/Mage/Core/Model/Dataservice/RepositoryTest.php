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
        $dataservice = (object)array();
        $name = 'name';
        $this->assertEquals($dataservice, $this->_repository->add($name, $dataservice)->get($name));
    }

    public function testAddGetNamespace()
    {
        $dataservice = (object)array();
        $nameInNamespace = 'name_in_namespace';
        $namespace = 'namespace';
        $name = 'name';
        $namespaceResults = $this->_repository->add($name, $dataservice)->addNameInNamespace(
            $namespace, $name, $nameInNamespace
        )->getByNamespace($namespace);
        $this->assertEquals($dataservice, $namespaceResults[$nameInNamespace]);
    }

    public function testGetChild()
    {
        $dataservice = (object)array();
        $name = 'name';
        $this->_repository->add($name, $dataservice);
        $this->assertEquals($dataservice, $this->_repository->getChild($name));
    }
}