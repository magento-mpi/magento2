<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor\Cache;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class PluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Css\PreProcessor\Cache\Plugin
     */
    protected $plugin;

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

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->cacheManagerMock = $this->getMock('Magento\Css\PreProcessor\Cache\CacheManager', [], [], '', false);
        $this->loggerMock = $this->getMock('Magento\Logger', [], [], '', false);
        $this->plugin = $this->objectManagerHelper->getObject(
            'Magento\Css\PreProcessor\Cache\Plugin',
            [
                'cacheManager' => $this->cacheManagerMock,
                'logger' => $this->loggerMock
            ]
        );
    }

    /**
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @param array $cacheManagerData
     * @param string|null $expected
     * @dataProvider aroundProcessDataProvider
     */
    public function testAroundProcess($arguments, $invocationChain, $cacheManagerData, $expected)
    {
        if (!empty($cacheManagerData)) {
            foreach ($cacheManagerData as $method => $info) {
                if ($method === 'getCachedFile') {
                    $this->cacheManagerMock->expects($this->once())
                        ->method($method)
                        ->will($this->returnValue($info['result']));
                } else {
                    $this->cacheManagerMock->expects($this->once())
                        ->method($method)
                        ->will($this->returnValue($info['result']));
                }

            }
        }
        $this->assertInstanceOf(
            'Magento\View\Publisher\CssFile', $this->plugin->aroundProcess($arguments, $invocationChain)
        );
    }

    /**
     * @return array
     */
    public function aroundProcessDataProvider()
    {
        /**
         * Prepare first item
         */
        $cssFileFirst = $this->getMock('Magento\View\Publisher\CssFile', [], [], '', false);
        $cssFileFirst->expects($this->once())
            ->method('getSourcePath')
            ->will($this->returnValue(false));

        $argFirst[] = $cssFileFirst;

        $expectedFirst = $this->getMock('Magento\View\Publisher\CssFile', [], [], '', false);
        $cssFileFirst->expects($this->once())
            ->method('buildUniquePath')
            ->will($this->returnValue('expectedFirst'));

        $invChainFirst = $this->getMock('Magento\Code\Plugin\InvocationChain', [], [], '', false);
        $invChainFirst->expects($this->once())
            ->method('proceed')
            ->with($this->equalTo($argFirst))
            ->will($this->returnValue($expectedFirst));

        /**
         * Prepare second item
         */
        $cssFileSecond = $this->getMock('Magento\View\Publisher\CssFile', [], [], '', false);
        $cssFileSecond->expects($this->once())
            ->method('getSourcePath')
            ->will($this->returnValue(false));

        $argSecond[] = $cssFileSecond;
        $invChainSecond = $this->getMock('Magento\Code\Plugin\InvocationChain', [], [], '', false);

        /**
         * Prepare third item
         */
        $cssFileThird = $this->getMock('Magento\View\Publisher\CssFile', [], [], '', false);
        $cssFileThird->expects($this->once())
            ->method('getSourcePath')
            ->will($this->returnValue(false));

        $argThird[] = $cssFileThird;

        $expectedThird = $this->getMock('Magento\View\Publisher\CssFile', [], [], '', false);

        $invChainThird = $this->getMock('Magento\Code\Plugin\InvocationChain', [], [], '', false);
        $invChainThird->expects($this->once())
            ->method('proceed')
            ->with($this->equalTo($argThird))
            ->will($this->returnValue($expectedThird));

        return [
            'source path already exist' => [
                'arguments' => $argFirst,
                'invocationChain' => $invChainFirst,
                'cacheManagerData' => [],
                'expected' => $expectedFirst
            ],
            'cached value exists' => [
                'arguments' => $argSecond,
                'invocationChain' => $invChainSecond,
                'cacheManagerData' => ['getCachedFile' => ['result' => $cssFileSecond]],
                'expected' => 'cached-value'
            ],
            'cached value does not exist' => [
                'arguments' => $argThird,
                'invocationChain' => $invChainThird,
                'cacheManagerData' => [
                    'getCachedFile' => ['result' => null],
                    'saveCache' => ['result' => 'self']
                ],
                'expected' => $expectedThird
            ],
        ];
    }

    public function testAroundProcessException()
    {
        $cssFile = $this->getMock('Magento\View\Publisher\CssFile', [], [], '', false);
        $cssFile->expects($this->once())
            ->method('getSourcePath')
            ->will($this->returnValue(false));

        $arguments[] = $cssFile;

        $this->cacheManagerMock->expects($this->once())
            ->method('getCachedFile')
            ->will($this->returnValue(null));

        $exception = new \Magento\Filesystem\FilesystemException('Test Message');
        $invocationChain = $this->getMock('Magento\Code\Plugin\InvocationChain', [], [], '', false);
        $invocationChain->expects($this->once())
            ->method('proceed')
            ->with($this->equalTo($arguments))
            ->will($this->throwException($exception));
        $this->loggerMock->expects($this->once())
            ->method('logException')
            ->with($this->equalTo($exception))
            ->will($this->returnSelf());
        $this->assertNull($this->plugin->aroundProcess($arguments, $invocationChain));
    }
}
