<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Framework\ObjectManager\Factory;

use Magento\Framework\ObjectManager\Config\Config;
use Magento\Framework\ObjectManager\Factory\Dynamic\Developer;
use Magento\Framework\ObjectManager\ObjectManager;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Expected exception message
     */
    const EXCEPTION_MESSAGE =
        'Invalid parameter configuration provided for $firstParam argument of Magento\Framework\ObjectManager';

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    protected function setUp()
    {
        $this->config = new Config();
        $this->factory = new Developer($this->config);
        $this->objectManager = new ObjectManager($this->factory, $this->config);
        $this->factory->setObjectManager($this->objectManager);
    }

    public function testCreateNoArgs()
    {
        $this->assertInstanceOf('StdClass', $this->factory->create('StdClass'));
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage \Magento\Framework\ObjectManager\Factory\FactoryTest::EXCEPTION_MESSAGE
     */
    public function testResolveArgumentsException()
    {
        $configMock = $this->getMock('\Magento\Framework\ObjectManager\Config\Config', array(), array(), '', false);
        $configMock->expects($this->once())->method('getArguments')
            ->will($this->returnValue(array(
                'firstParam' => 1
            )));

        $definitionsMock = $this->getMock('Magento\Framework\ObjectManager\DefinitionInterface');
        $definitionsMock->expects($this->once())->method('getParameters')
            ->will($this->returnValue(array(array(
                'firstParam', 'string', true, 'default_val'
            ))));

        $this->factory = new Developer(
            $configMock,
            null,
            $definitionsMock
        );
        $this->objectManager = new ObjectManager($this->factory, $this->config);
        $this->factory->setObjectManager($this->objectManager);
        $this->factory->create('Magento\Framework\ObjectManager\Factory\Fixture\OneScalar', array('foo' => 'bar'));
    }

    public function testCreateOneArg()
    {
        /** @var \Magento\Framework\ObjectManager\Factory\Fixture\OneScalar $result */
        $result = $this->factory->create(
            'Magento\Framework\ObjectManager\Factory\Fixture\OneScalar',
            array('foo' => 'bar')
        );
        $this->assertInstanceOf('\Magento\Framework\ObjectManager\Factory\Fixture\OneScalar', $result);
        $this->assertEquals('bar', $result->getFoo());
    }

    public function testCreateWithInjectable()
    {
        // let's imitate that One is injectable by providing DI configuration for it
        $this->config->extend(
            array(
                'Magento\Framework\ObjectManager\Factory\Fixture\OneScalar' => array(
                    'arguments' => array('foo' => 'bar')
                )
            )
        );
        /** @var \Magento\Framework\ObjectManager\Factory\Fixture\Two $result */
        $result = $this->factory->create('Magento\Framework\ObjectManager\Factory\Fixture\Two');
        $this->assertInstanceOf('\Magento\Framework\ObjectManager\Factory\Fixture\Two', $result);
        $this->assertInstanceOf('\Magento\Framework\ObjectManager\Factory\Fixture\OneScalar', $result->getOne());
        $this->assertEquals('bar', $result->getOne()->getFoo());
        $this->assertEquals('optional', $result->getBaz());
    }

    /**
     * @param string $startingClass
     * @param string $terminationClass
     * @dataProvider circularDataProvider
     */
    public function testCircular($startingClass, $terminationClass)
    {
        $this->setExpectedException(
            '\LogicException',
            sprintf('Circular dependency: %s depends on %s and vice versa.', $startingClass, $terminationClass)
        );
        $this->factory->create($startingClass);
    }

    /**
     * @return array
     */
    public function circularDataProvider()
    {
        $prefix = 'Magento\Framework\ObjectManager\Factory\Fixture\\';
        return array(
            array("{$prefix}CircularOne", "{$prefix}CircularThree"),
            array("{$prefix}CircularTwo", "{$prefix}CircularOne"),
            array("{$prefix}CircularThree", "{$prefix}CircularTwo")
        );
    }

    public function testCreateUsingReflection()
    {
        $type = 'Magento\Framework\ObjectManager\Factory\Fixture\Polymorphous';
        $definitions = $this->getMock('Magento\Framework\ObjectManager\DefinitionInterface');
        // should be more than defined in "switch" of create() method
        $definitions->expects($this->once())->method('getParameters')->with($type)->will($this->returnValue(array(
            array('one', null, false, null), array('two', null, false, null), array('three', null, false, null),
            array('four', null, false, null), array('five', null, false, null), array('six', null, false, null),
            array('seven', null, false, null), array('eight', null, false, null), array('nine', null, false, null),
            array('ten', null, false, null),
        )));
        $factory = new Developer($this->config, null, $definitions);
        $result = $factory->create($type, array(
            'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
            'six' => 6, 'seven' => 7, 'eight' => 8, 'nine' => 9, 'ten' => 10,
        ));
        $this->assertSame(10, $result->getArg(9));
    }
}
