<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\App\View\Deployment;

use Magento\Framework\App\View\Deployment\Version;

class VersionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Version
     */
    private $object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $appState;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $versionStorage;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $versionGenerator;

    protected function setUp()
    {
        $this->appState = $this->getMock('Magento\Framework\App\State', array(), array(), '', false);
        $this->versionStorage = $this->getMock('Magento\Framework\App\View\Deployment\Version\StorageInterface');
        $this->versionGenerator = $this->getMock('Magento\Framework\App\View\Deployment\Version\GeneratorInterface');
        $this->object = new Version($this->appState, $this->versionStorage, $this->versionGenerator);
    }

    public function testGetValueDeveloperMode()
    {
        $this->appState
            ->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue(\Magento\Framework\App\State::MODE_DEVELOPER))
        ;
        $this->versionStorage->expects($this->never())->method($this->anything());
        $this->versionGenerator->expects($this->once())->method('generate')->will($this->returnValue('123'));
        $this->assertEquals('123', $this->object->getValue());
        $this->object->getValue(); // Ensure computation occurs only once and result is cached in memory
    }

    /**
     * @param string $appMode
     * @dataProvider getValueFromStorageDataProvider
     */
    public function testGetValueFromStorage($appMode)
    {
        $this->appState
            ->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue($appMode))
        ;
        $this->versionStorage->expects($this->once())->method('load')->will($this->returnValue('123'));
        $this->versionStorage->expects($this->never())->method('save');
        $this->versionGenerator->expects($this->never())->method('generate');
        $this->assertEquals('123', $this->object->getValue());
        $this->object->getValue(); // Ensure caching in memory
    }

    public function getValueFromStorageDataProvider()
    {
        return array(
            'default mode'      => array(\Magento\Framework\App\State::MODE_DEFAULT),
            'production mode'   => array(\Magento\Framework\App\State::MODE_PRODUCTION),
            'arbitrary mode'    => array('test'),
        );
    }

    public function testGetValueDefaultModeSaving()
    {
        $this->appState
            ->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue(\Magento\Framework\App\State::MODE_DEFAULT))
        ;
        $storageException = new \UnexpectedValueException('Does not exist in the storage');
        $this->versionStorage
            ->expects($this->once())
            ->method('load')
            ->will($this->throwException($storageException))
        ;
        $this->versionGenerator->expects($this->once())->method('generate')->will($this->returnValue('123'));
        $this->versionStorage->expects($this->once())->method('save')->with('123');
        $this->assertEquals('123', $this->object->getValue());
        $this->object->getValue(); // Ensure caching in memory
    }
}
