<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Less\Cache;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class PluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Css\PreProcessor\Cache\CacheManager
     */
    protected $cacheManagerMock;

    /**
     * @var \Magento\Logger
     */
    protected $loggerMock;

    /**
     * @var \Magento\Less\Cache\Plugin
     */
    protected $plugin;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->cacheManagerMock = $this->getMock('Magento\Css\PreProcessor\Cache\CacheManager', [], [], '', false);
        $this->loggerMock = $this->getMock('Magento\Logger', [], [], '', false);
        $this->plugin = $this->objectManagerHelper->getObject(
            'Magento\Less\Cache\Plugin',
            [
                'cacheManager' => $this->cacheManagerMock,
                'logger' => $this->loggerMock
            ]
        );
    }

    public function testBeforeProcessLessInstructions()
    {
        $arguments = ['some\less\filePth.less', ['some', 'kind', 'of' ,'params']];
        list($lessFilePath, $params) = $arguments;

        $this->cacheManagerMock->expects($this->once())
            ->method('addToCache')
            ->with(
                \Magento\Css\PreProcessor\Cache\Import\Cache::IMPORT_CACHE,
                $this->equalTo(array($lessFilePath, $params))
            )
            ->will($this->returnSelf());

        $this->assertEquals($arguments, $this->plugin->beforeProcessLessInstructions($arguments));
    }
}
