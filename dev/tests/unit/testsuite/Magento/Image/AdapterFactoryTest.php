<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Image;

class AdapterFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Image\Adapter\ConfigInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    public function setUp()
    {
        $this->configMock = $this->getMock(
            'Magento\Image\Adapter\ConfigInterface', array('getAdapterAlias', 'getAdapters'), array(), '', false
        );

        $this->configMock->expects($this->once())
            ->method('getAdapters')
            ->will($this->returnValue(array(
                'GD2'           => array('class' => 'Magento\Image\Adapter\Gd2'),
                'IMAGEMAGICK'   => array('class' => 'Magento\Image\Adapter\ImageMagick'),
                'wrongInstance' => array('class' => 'stdClass'),
                'test'          => array(),
            )));
    }

    /**
     * @dataProvider createDataProvider
     * @param string $alias
     * @param string $class
     */
    public function testCreate($alias, $class)
    {
        $objectManagerMock = $this->getMock('Magento\ObjectManager\ObjectManager', array('create'), array(), '', false);
        $imageAdapterMock = $this->getMock($class, array('checkDependencies'), array(), '', false);
        $imageAdapterMock->expects($this->once())
            ->method('checkDependencies');

        $objectManagerMock->expects($this->once())
            ->method('create')
            ->with($class)
            ->will($this->returnValue($imageAdapterMock));

        $adapterFactory = new AdapterFactory($objectManagerMock, $this->configMock);
        $imageAdapter = $adapterFactory->create($alias);
        $this->assertInstanceOf($class, $imageAdapter);
    }

    /**
     * @see self::testCreate()
     * @return array
     */
    public function createDataProvider()
    {
        return array(
            array('GD2', 'Magento\Image\Adapter\Gd2'),
            array('IMAGEMAGICK', 'Magento\Image\Adapter\ImageMagick'),
        );
    }

    /**
     * @covers \Magento\Image\AdapterFactory::create
     */
    public function testCreateWithoutName()
    {
        $adapterAlias = 'IMAGEMAGICK';
        $adapterClass = 'Magento\Image\Adapter\ImageMagick';

        $this->configMock->expects($this->once())
            ->method('getAdapterAlias')
            ->will($this->returnValue($adapterAlias));

        $objectManagerMock = $this->getMock('Magento\ObjectManager\ObjectManager', array('create'), array(), '', false);
        $imageAdapterMock = $this->getMock($adapterClass, array('checkDependencies'), array(), '', false);
        $imageAdapterMock->expects($this->once())
            ->method('checkDependencies');

        $objectManagerMock->expects($this->once())
            ->method('create')
            ->with($adapterClass)
            ->will($this->returnValue($imageAdapterMock));

        $adapterFactory = new AdapterFactory($objectManagerMock, $this->configMock);
        $imageAdapter = $adapterFactory->create();
        $this->assertInstanceOf($adapterClass, $imageAdapter);
    }

    /**
     * @covers \Magento\Image\AdapterFactory::create
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Image adapter is not selected.
     */
    public function testInvalidArgumentException()
    {
        $this->configMock->expects($this->once())
            ->method('getAdapterAlias')
            ->will($this->returnValue(''));
        $objectManagerMock = $this->getMock('Magento\ObjectManager\ObjectManager', array('create'), array(), '', false);
        $adapterFactory = new AdapterFactory($objectManagerMock, $this->configMock);
        $adapterFactory->create();
    }

    /**
     * @covers \Magento\Image\AdapterFactory::create
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Image adapter for 'test' is not setup.
     */
    public function testNonAdapterClass()
    {
        $alias = 'test';
        $objectManagerMock = $this->getMock('Magento\ObjectManager\ObjectManager', array('create'), array(), '', false);

        $adapterFactory = new AdapterFactory($objectManagerMock, $this->configMock);
        $adapterFactory->create($alias);
    }

    /**
     * @covers \Magento\Image\AdapterFactory::create
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage stdClass is not instance of \Magento\Image\Adapter\AdapterInterface
     */
    public function testWrongInstance()
    {
        $alias = 'wrongInstance';
        $class = 'stdClass';
        $objectManagerMock = $this->getMock('Magento\ObjectManager\ObjectManager', array('create'), array(), '', false);
        $imageAdapterMock = $this->getMock($class, array('checkDependencies'), array(), '', false);

        $objectManagerMock->expects($this->once())
            ->method('create')
            ->with($class)
            ->will($this->returnValue($imageAdapterMock));

        $adapterFactory = new AdapterFactory($objectManagerMock, $this->configMock);
        $adapterFactory->create($alias);
    }
}
