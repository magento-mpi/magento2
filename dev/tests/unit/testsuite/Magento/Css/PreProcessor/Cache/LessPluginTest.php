<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor\Cache;

class LessPluginTest extends \PHPUnit_Framework_TestCase
{

    public function testBeforeProcessLessInstructions()
    {
        $cacheManagerFactoryMock = $this->getMock(
            'Magento\Css\PreProcessor\Cache\CacheManagerInitializer',
            [],
            [],
            '',
            false
        );
        $subjectMock = $this->getMock('Magento\Less\PreProcessor', array(), array(), '', false);
        $plugin = new \Magento\Css\PreProcessor\Cache\LessPlugin($cacheManagerFactoryMock);
        $cacheManager = $this->getMock('Magento\Css\PreProcessor\Cache\CacheManager', [], [], '', false);
        $cacheManagerFactoryMock->expects($this->any())
            ->method('getCacheManager')
            ->will($this->returnValue($cacheManager));
        $arguments = ['some\less\filePth.less', ['some', 'kind', 'of' ,'params']];
        list($lessFilePath, $params) = $arguments;

        $cacheManager->expects($this->once())
            ->method('addEntityToCache')
            ->with($this->equalTo($lessFilePath), $this->equalTo($params))
            ->will($this->returnSelf());

        $plugin->beforeProcessLessInstructions($subjectMock,
            'some\less\filePth.less', ['some', 'kind', 'of', 'params']);
    }
}
