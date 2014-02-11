<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ObjectManager\Config\Argument;

class ObjectFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\ObjectManager
     */
    private $objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\ObjectManager\Config
     */
    private $config;

    protected function setUp()
    {
        $this->objectManager = $this->getMockForAbstractClass('\Magento\ObjectManager');
        $this->config = $this->getMockForAbstractClass('\Magento\ObjectManager\Config');
    }

    public function testSetGetObjectManager()
    {
        $factory = new ObjectFactory($this->config);
        $factory->setObjectManager($this->objectManager);
        $this->objectManager->expects($this->once())->method('create');
        $factory->create('type', false);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Object manager has not been assigned yet.
     */
    public function testGetObjectManagerException()
    {
        $factory = new ObjectFactory($this->config);
        $factory->create('type', false);
    }

    /**
     * @param bool $isShared
     * @param string $expectedMethod
     * @dataProvider isSharedDataProvider
     */
    public function testCreateLookup($isShared, $expectedMethod)
    {
        $value = new \StdClass;
        $factory = new ObjectFactory($this->config, $this->objectManager);
        $this->objectManager->expects($this->once())->method($expectedMethod)->with('type')->will($this->returnValue($value));
        $this->config->expects($this->once())->method('isShared')->with('type')->will($this->returnValue($isShared));
        $this->assertSame($value, $factory->create('type'));
    }

    /**
     * @return array
     */
    public function isSharedDataProvider()
    {
        return array(
            array(true, 'get'),
            array(false, 'create'),
        );
    }

    /**
     * @param bool $isShared
     * @param string $expectedMethod
     * @dataProvider isSharedDataProvider
     */
    public function testCreateNoLookup($isShared, $expectedMethod)
    {
        $factory = new ObjectFactory($this->config, $this->objectManager);
        $this->objectManager->expects($this->once())->method($expectedMethod)->with('type');
        $this->config->expects($this->never())->method('isShared');
        $factory->create('type', $isShared);
    }
}
