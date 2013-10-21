<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filter;

class FilterManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Filter\FilterManager
     */
    protected $filterManager;

    /**
     * @var \Magento\Filter\Factory
     */
    protected $factoryMock;

    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Filter\FilterManager\Config
     */
    protected $config;

    protected function initMocks()
    {
        $factoryName = 'Magento\Filter\Factory';
        $this->factoryMock = $this->getMock($factoryName, array('canCreateFilter', 'createFilter'), array(), '', false);
        $this->objectManager = $this->getMockForAbstractClass('\Magento\ObjectManager', array(), '', true, true,
            true, array('create'));
        $this->objectManager->expects($this->atLeastOnce())->method('create')
            ->with($this->equalTo($factoryName))
            ->will($this->returnValue($this->factoryMock));
        $this->config = $this->getMock('\Magento\Filter\FilterManager\Config', array('getFactories'),
            array(), '', false);
        $this->config->expects($this->atLeastOnce())->method('getFactories')
            ->will($this->returnValue(array($factoryName)));
        $this->filterManager = new \Magento\Filter\FilterManager($this->objectManager, $this->config);
    }

    public function testGetFilterFactories()
    {
        $this->initMocks();
        $method = new \ReflectionMethod('Magento\Filter\FilterManager', 'getFilterFactories');
        $method->setAccessible(true);
        $this->assertEquals(array($this->factoryMock), $method->invoke($this->filterManager));
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Filter factory must implement FilterFactoryInterface interface, stdClass was given.
     */
    public function testGetFilterFactoriesWrongInstance()
    {
        $factoryName = 'Magento\Filter\Factory';
        $this->factoryMock = new \stdClass();
        $this->objectManager = $this->getMockForAbstractClass('\Magento\ObjectManager', array(), '', true, true,
            true, array('create'));
        $this->objectManager->expects($this->atLeastOnce())->method('create')
            ->with($this->equalTo($factoryName))
            ->will($this->returnValue($this->factoryMock));
        $this->config = $this->getMock('\Magento\Filter\FilterManager\Config',
            array('getFactories'), array(), '', false);
        $this->config->expects($this->atLeastOnce())->method('getFactories')
            ->will($this->returnValue(array($factoryName)));
        $this->filterManager = new \Magento\Filter\FilterManager($this->objectManager, $this->config);

        $method = new \ReflectionMethod('Magento\Filter\FilterManager', 'getFilterFactories');
        $method->setAccessible(true);
        $method->invoke($this->filterManager);
    }

    public function testCreateFilterInstance()
    {
        $this->initMocks();
        $filterMock = $this->getMock('FactoryInterface');
        $this->configureFactoryMock($filterMock, 'alias', array('123'));


        $method = new \ReflectionMethod('Magento\Filter\FilterManager', 'createFilterInstance');
        $method->setAccessible(true);
        $this->assertEquals($filterMock, $method->invoke($this->filterManager, 'alias', array('123')));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Filter was not found by given alias wrongAlias
     */
    public function testCreateFilterInstanceWrongAlias()
    {
        $this->initMocks();
        $filterAlias = 'wrongAlias';
        $this->factoryMock->expects($this->atLeastOnce())->method('canCreateFilter')
            ->with($this->equalTo($filterAlias))
            ->will($this->returnValue(false));

        $method = new \ReflectionMethod('Magento\Filter\FilterManager', 'createFilterInstance');
        $method->setAccessible(true);
        $method->invoke($this->filterManager, $filterAlias, array());
    }

    /**
     * @param object $filter
     * @param string $alias
     * @param array $arguments
     */
    protected function configureFactoryMock($filter, $alias, $arguments = array())
    {
        $this->factoryMock->expects($this->atLeastOnce())->method('canCreateFilter')
            ->with($this->equalTo($alias))
            ->will($this->returnValue(true));

        $this->factoryMock->expects($this->atLeastOnce())->method('createFilter')
            ->with($this->equalTo($alias), $this->equalTo($arguments))
            ->will($this->returnValue($filter));
    }

    public function testCall()
    {
        $value = 'testValue';
        $this->initMocks();
        $filterMock = $this->getMock('FactoryInterface', array('filter'));
        $filterMock->expects($this->atLeastOnce())->method('filter')
            ->with($this->equalTo($value))
            ->will($this->returnValue($value));
        $this->configureFactoryMock($filterMock, 'alias', array('123'));
        $this->assertEquals($value, $this->filterManager->alias($value, array('123')));
    }
}
