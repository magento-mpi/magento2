<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filter;

class AbstractFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Filter\AbstractFactory
     */
    protected $factory;

    /**
     * @var array
     */
    protected $invokableList = array(
        'sprintf'       => 'Magento\Filter\Sprintf',
        'template'      => 'Magento\Filter\Template',
        'arrayFilter'   => 'Magento\Filter\ArrayFilter',
    );

    /**
     * @var array
     */
    protected $sharedList = array(
        'Magento\Filter\Template' => true,
        'Magento\Filter\ArrayFilter' => false,
    );

    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManger;

    public function setUp()
    {
        $this->objectManager = $this->getMockForAbstractClass('\Magento\ObjectManager', array(), '', true, true,
            true, array('create'));

        $this->factory = $this->getMockForAbstractClass('Magento\Filter\AbstractFactory', array(
            'objectManger' => $this->objectManager
        ));
        $property = new \ReflectionProperty('Magento\Filter\AbstractFactory', 'invokableClasses');
        $property->setAccessible(true);
        $property->setValue($this->factory, $this->invokableList);

        $property = new \ReflectionProperty('Magento\Filter\AbstractFactory', 'shared');
        $property->setAccessible(true);
        $property->setValue($this->factory, $this->sharedList);
    }

    /**
     * @dataProvider canCreateFilterDataProvider
     * @param string $alias
     * @param bool $expectedResult
     */
    public function testCanCreateFilter($alias, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->factory->canCreateFilter($alias));
    }

    /**
     * @return array
     */
    public function canCreateFilterDataProvider()
    {
        return array(
            array('arrayFilter', true),
            array('notExist', false),
        );
    }

    /**
     * @dataProvider isSharedDataProvider
     * @param string $alias
     * @param bool $expectedResult
     */
    public function testIsShared($alias, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->factory->isShared($alias));
    }

    /**
     * @return array
     */
    public function isSharedDataProvider()
    {
        return array(
            'shared' => array('Magento\Filter\Template', true),
            'not shared' => array('Magento\Filter\ArrayFilter', false),
            'default value' => array('Magento\Filter\Sprintf', true),
        );
    }

    /**
     * @dataProvider createFilterDataProvider
     * @param string $alias
     * @param array $arguments
     * @param bool $isShared
     */
    public function testCreateFilter($alias, $arguments, $isShared)
    {
        $property = new \ReflectionProperty('Magento\Filter\AbstractFactory', 'sharedInstances');
        $property->setAccessible(true);

        $filterMock = $this->getMock('FactoryInterface', array('filter'));
        $this->objectManager->expects($this->atLeastOnce())->method('create')
            ->with($this->equalTo($this->invokableList[$alias]), $this->equalTo($arguments))
            ->will($this->returnValue($filterMock));

        $this->assertEquals($filterMock, $this->factory->createFilter($alias, $arguments));
        if ($isShared) {
            $sharedList = $property->getValue($this->factory);
            $this->assertTrue(array_key_exists($alias, $sharedList));
            $this->assertEquals($filterMock, $sharedList[$alias]);
        } else {
            $this->assertEmpty($property->getValue($this->factory));
        }
    }

    /**
     * @return array
     */
    public function createFilterDataProvider()
    {
        return array(
            'not shared with args' => array('arrayFilter', array('123', '231'), false),
            'not shared without args' => array('arrayFilter', array(), true),
            'shared' => array('template', array(), true),
            'default shared' => array('sprintf', array(), true),
        );
    }
}
