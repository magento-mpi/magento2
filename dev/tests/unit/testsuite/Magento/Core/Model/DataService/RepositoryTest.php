<?php
/**
 * \Magento\Core\Model\DataService\Repository
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\DataService;

class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\DataService\Repository
     */
    protected $_repository;

    public function setUp()
    {
        $this->_repository = new \Magento\Core\Model\DataService\Repository();
    }

    public function testAddGet()
    {
        $data = array();
        $serviceName = 'service_name';
        $this->assertEquals($data, $this->_repository->add($serviceName, $data)->get($serviceName));
    }

    public function testGet()
    {
        $this->assertEquals(null, $this->_repository->get('unknown_service_name'));
    }

    public function testGetByNamespace()
    {
        $result = $this->_repository->getByNamespace('unknown_namespace');
        $this->assertEquals(array(), $result);
    }

    public function testAddGetNamespace()
    {
        $data = array();
        $alias = 'alias';
        $namespace = 'namespace';
        $serviceName = 'service_name';
        $namespaceResults = $this->_repository->add($serviceName, $data)
            ->setAlias($namespace, $serviceName, $alias)
            ->getByNamespace($namespace);
        $this->assertEquals($data, $namespaceResults[$alias]);
    }

    public function testAddGetNamespaceAgain()
    {
        $data = array();
        $alias = 'alias';
        $namespace = 'namespace';
        $serviceName = 'service_name';
        $namespaceResults = $this->_repository->add($serviceName, $data)
            ->setAlias($namespace, $serviceName, 'something_different')
            ->setAlias($namespace, $serviceName, $alias)
            ->getByNamespace($namespace);
        $this->assertEquals($data, $namespaceResults[$alias]);
    }

    public function testGetChild()
    {
        $data = array();
        $serviceName = 'service_name';
        $this->_repository->add($serviceName, $data);
        $this->assertEquals($data, $this->_repository->getChildNode($serviceName));
    }
}
