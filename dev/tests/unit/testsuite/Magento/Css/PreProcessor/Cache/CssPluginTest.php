<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor\Cache;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class CssPluginTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Css\PreProcessor\Cache\Plugin */
    protected $plugin;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Css\PreProcessor\Cache\CacheManagerFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $cacheManagerFactoryMock;

    /** @var \Magento\Css\PreProcessor\Cache\CacheManager|\PHPUnit_Framework_MockObject_MockObject */
    protected $cacheManager;

    /** @var \Magento\Logger|\PHPUnit_Framework_MockObject_MockObject */
    protected $loggerMock;

    /** @var \Magento\Filesystem\Directory\WriteInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $targetDirMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    protected function setUp()
    {
        $this->targetDirMock = $this->getMock('Magento\Filesystem\Directory\WriteInterface', [], [], '', false);
        $this->cacheManagerFactoryMock = $this->getMock(
            'Magento\Css\PreProcessor\Cache\CacheManagerInitializer',
            [],
            [],
            '',
            false
        );
        $this->cacheManager = $this->getMock('Magento\Css\PreProcessor\Cache\CacheManager', [], [], '', false);
        $this->cacheManagerFactoryMock->expects($this->any())
            ->method('getCacheManager')
            ->will($this->returnValue($this->cacheManager));
        $this->loggerMock = $this->getMock('Magento\Logger', [], [], '', false);

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->subjectMock = $this->getMock('Magento\Css\PreProcessor\Less', array(), array(), '', false);
        $this->plugin = $this->objectManagerHelper->getObject(
            'Magento\Css\PreProcessor\Cache\CssPlugin',
            [
                'initializer' => $this->cacheManagerFactoryMock,
                'logger' => $this->loggerMock
            ]
        );
    }

    /**
     * @param string $arguments
     * @param \Closure $closureMock
     * @param array $cacheManagerData
     * @param string|null $expected
     * @dataProvider aroundProcessDataProvider
     */
    public function testAroundProcess($arguments, $closureMock, $cacheManagerData, $expected)
    {
        if (!empty($cacheManagerData)) {
            foreach ($cacheManagerData as $method => $info) {
                if ($method === 'getCachedFile') {
                    $this->cacheManager->expects($this->once())
                        ->method($method)
                        ->will($this->returnValue($info['result']));
                } else {
                    $this->cacheManager->expects($this->once())
                        ->method($method)
                        ->with($this->equalTo($info['with']))
                        ->will($this->returnValue($info['result']));
                }

            }
        }
        $this->assertEquals($expected, $this->plugin->aroundProcess($this->subjectMock, $closureMock, 'css\style.less', array(), $this->targetDirMock, $arguments));
    }

    /**
     * @return array
     */
    public function aroundProcessDataProvider()
    {
        $expectedFirst = 'expectedFirst';
        $closureFirst = function () use ($expectedFirst) {
            return $expectedFirst;
        };

        $closureSecond = function () {

        };

        $expectedThird = 'expectedThird';

        $closureThird = function () use ($expectedThird) {
            return $expectedThird;
        };

        return [
            'source path already exist' => [
                'arguments' => 'css\style.css',
                'closure' => $closureFirst,
                'cacheManagerData' => [],
                'expected' => $expectedFirst
            ],
            'cached value exists' => [
                'arguments' => null,
                'closure' => $closureSecond,
                'cacheManagerData' => ['getCachedFile' => ['result' => 'cached-value']],
                'expected' => 'cached-value'
            ],
            'cached value does not exist' => [
                'arguments' => null,
                'closure' => $closureThird,
                'cacheManagerData' => [
                    'getCachedFile' => ['result' => null],
                    'saveCache' => ['with' => $expectedThird, 'result' => 'self']
                ],
                'expected' => $expectedThird
            ],
        ];
    }
}
