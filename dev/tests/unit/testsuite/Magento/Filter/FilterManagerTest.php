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
    protected $_filterManager;

    /**
     * @var \Magento\Filter\Factory
     */
    protected $_factoryMock;

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Filter\FilterManager\Config
     */
    protected $_config;

    protected function initMocks()
    {
        $factoryName = 'Magento\Filter\Factory';
        $this->_factoryMock = $this->getMock(
            $factoryName,
            array('canCreateFilter', 'createFilter'),
            array(),
            '',
            false
        );
        $this->_objectManager = $this->getMockForAbstractClass(
            '\Magento\Framework\ObjectManager',
            array(),
            '',
            true,
            true,
            true,
            array('create')
        );
        $this->_objectManager->expects(
            $this->atLeastOnce()
        )->method(
            'create'
        )->with(
            $this->equalTo($factoryName)
        )->will(
            $this->returnValue($this->_factoryMock)
        );
        $this->_config = $this->getMock(
            '\Magento\Filter\FilterManager\Config',
            array('getFactories'),
            array(),
            '',
            false
        );
        $this->_config->expects(
            $this->atLeastOnce()
        )->method(
            'getFactories'
        )->will(
            $this->returnValue(array($factoryName))
        );
        $this->_filterManager = new \Magento\Filter\FilterManager($this->_objectManager, $this->_config);
    }

    public function testGetFilterFactories()
    {
        $this->initMocks();
        $method = new \ReflectionMethod('Magento\Filter\FilterManager', 'getFilterFactories');
        $method->setAccessible(true);
        $this->assertEquals(array($this->_factoryMock), $method->invoke($this->_filterManager));
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Filter factory must implement FilterFactoryInterface interface, stdClass was given.
     */
    public function testGetFilterFactoriesWrongInstance()
    {
        $factoryName = 'Magento\Filter\Factory';
        $this->_factoryMock = new \stdClass();
        $this->_objectManager = $this->getMockForAbstractClass(
            '\Magento\Framework\ObjectManager',
            array(),
            '',
            true,
            true,
            true,
            array('create')
        );
        $this->_objectManager->expects(
            $this->atLeastOnce()
        )->method(
            'create'
        )->with(
            $this->equalTo($factoryName)
        )->will(
            $this->returnValue($this->_factoryMock)
        );
        $this->_config = $this->getMock(
            '\Magento\Filter\FilterManager\Config',
            array('getFactories'),
            array(),
            '',
            false
        );
        $this->_config->expects(
            $this->atLeastOnce()
        )->method(
            'getFactories'
        )->will(
            $this->returnValue(array($factoryName))
        );
        $this->_filterManager = new \Magento\Filter\FilterManager($this->_objectManager, $this->_config);

        $method = new \ReflectionMethod('Magento\Filter\FilterManager', 'getFilterFactories');
        $method->setAccessible(true);
        $method->invoke($this->_filterManager);
    }

    public function testCreateFilterInstance()
    {
        $this->initMocks();
        $filterMock = $this->getMock('FactoryInterface');
        $this->configureFactoryMock($filterMock, 'alias', array('123'));


        $method = new \ReflectionMethod('Magento\Filter\FilterManager', 'createFilterInstance');
        $method->setAccessible(true);
        $this->assertEquals($filterMock, $method->invoke($this->_filterManager, 'alias', array('123')));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Filter was not found by given alias wrongAlias
     */
    public function testCreateFilterInstanceWrongAlias()
    {
        $this->initMocks();
        $filterAlias = 'wrongAlias';
        $this->_factoryMock->expects(
            $this->atLeastOnce()
        )->method(
            'canCreateFilter'
        )->with(
            $this->equalTo($filterAlias)
        )->will(
            $this->returnValue(false)
        );

        $method = new \ReflectionMethod('Magento\Filter\FilterManager', 'createFilterInstance');
        $method->setAccessible(true);
        $method->invoke($this->_filterManager, $filterAlias, array());
    }

    /**
     * @param object $filter
     * @param string $alias
     * @param array $arguments
     */
    protected function configureFactoryMock($filter, $alias, $arguments = array())
    {
        $this->_factoryMock->expects(
            $this->atLeastOnce()
        )->method(
            'canCreateFilter'
        )->with(
            $this->equalTo($alias)
        )->will(
            $this->returnValue(true)
        );

        $this->_factoryMock->expects(
            $this->atLeastOnce()
        )->method(
            'createFilter'
        )->with(
            $this->equalTo($alias),
            $this->equalTo($arguments)
        )->will(
            $this->returnValue($filter)
        );
    }

    public function testCall()
    {
        $value = 'testValue';
        $this->initMocks();
        $filterMock = $this->getMock('FactoryInterface', array('filter'));
        $filterMock->expects(
            $this->atLeastOnce()
        )->method(
            'filter'
        )->with(
            $this->equalTo($value)
        )->will(
            $this->returnValue($value)
        );
        $this->configureFactoryMock($filterMock, 'alias', array('123'));
        $this->assertEquals($value, $this->_filterManager->alias($value, array('123')));
    }
}
