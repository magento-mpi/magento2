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
     * @dataProvider createDataProvider
     * @param string $class
     */
    public function testCreate($class)
    {
        $configMock = $this->getMock('Magento\Core\Model\Image\Adapter\Config', array(), array(), '', false);
        $objectManagerMock = $this->getMock('Magento\ObjectManager\ObjectManager', array('create'), array(), '', false);
        $imageAdapterMock = $this->getMock($class, array('checkDependencies'), array(), '', false);
        $imageAdapterMock->expects($this->once())
            ->method('checkDependencies');

        $objectManagerMock->expects($this->once())
            ->method('create')
            ->with($class)
            ->will($this->returnValue($imageAdapterMock));

        $adapterFactory = new AdapterFactory($objectManagerMock, $configMock);
        $imageAdapter = $adapterFactory->create($class);
        $this->assertInstanceOf($class, $imageAdapter);
    }

    /**
     * @see self::testCreate()
     * @return array
     */
    public function createDataProvider()
    {
        return array(
            array('Magento\Image\Adapter\Gd2'),
            array('Magento\Image\Adapter\ImageMagick'),
        );
    }

    /**
     * @covers \Magento\Image\AdapterFactory::create
     */
    public function testCreateWithoutName()
    {
        $class = 'Magento\Image\Adapter\ImageMagick';
        $configMock = $this->getMock(
            'Magento\Core\Model\Image\Adapter\Config', array('getAdapterName'), array(), '', false
        );
        $configMock->expects($this->once())
            ->method('getAdapterName')
            ->will($this->returnValue($class));

        $objectManagerMock = $this->getMock('Magento\ObjectManager\ObjectManager', array('create'), array(), '', false);
        $imageAdapterMock = $this->getMock($class, array('checkDependencies'), array(), '', false);
        $imageAdapterMock->expects($this->once())
            ->method('checkDependencies');

        $objectManagerMock->expects($this->once())
            ->method('create')
            ->with($class)
            ->will($this->returnValue($imageAdapterMock));

        $adapterFactory = new AdapterFactory($objectManagerMock, $configMock);
        $imageAdapter = $adapterFactory->create();
        $this->assertInstanceOf($class, $imageAdapter);
    }

    /**
     * @covers \Magento\Image\AdapterFactory::create
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Image adapter is not selected.
     */
    public function testInvalidArgumentException()
    {
        $configMock = $this->getMock(
            'Magento\Core\Model\Image\Adapter\Config', array('getAdapterName'), array(), '', false
        );
        $configMock->expects($this->once())
            ->method('getAdapterName')
            ->will($this->returnValue(''));
        $objectManagerMock = $this->getMock('Magento\ObjectManager\ObjectManager', array('create'), array(), '', false);
        $adapterFactory = new AdapterFactory($objectManagerMock, $configMock);
        $adapterFactory->create();
    }

    /**
     * @covers \Magento\Image\AdapterFactory::create
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage stdClass is not instance of \Magento\Image\Adapter\AdapterInterface
     */
    public function testWrongInstance()
    {
        $class = 'stdClass';
        $configMock = $this->getMock('Magento\Core\Model\Image\Adapter\Config', array(), array(), '', false);
        $objectManagerMock = $this->getMock('Magento\ObjectManager\ObjectManager', array('create'), array(), '', false);
        $imageAdapterMock = $this->getMock($class, array('checkDependencies'), array(), '', false);

        $objectManagerMock->expects($this->once())
            ->method('create')
            ->with($class)
            ->will($this->returnValue($imageAdapterMock));

        $adapterFactory = new AdapterFactory($objectManagerMock, $configMock);
        $adapterFactory->create($class);
    }
}
