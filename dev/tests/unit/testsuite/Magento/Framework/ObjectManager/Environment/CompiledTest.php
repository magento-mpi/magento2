<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\ObjectManager\Environment;

class CompiledTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\ObjectManager\Environment\Compiled
     */
    protected $_compiled;

    protected function setUp()
    {
        $envFactoryMock = $this->getMock('Magento\Framework\ObjectManager\EnvironmentFactory', [], [], '', false);
        $this->_compiled = new \Magento\Framework\ObjectManager\Environment\Compiled($envFactoryMock);
    }

    public function testGetFilePath()
    {
        $this->assertContains('/var/di/global.ser', $this->_compiled->getFilePath());
    }

    public function testGetMode()
    {
        $this->assertEquals('compiled', $this->_compiled->getMode());
    }

    public function testGetObjectManagerConfigLoader()
    {
        $this->assertInstanceOf(
            'Magento\Framework\App\ObjectManager\ConfigLoader\Compiled',
            $this->_compiled->getObjectManagerConfigLoader()
        );
    }
}
