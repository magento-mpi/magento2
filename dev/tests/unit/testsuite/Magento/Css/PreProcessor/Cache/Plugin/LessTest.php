<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Css\PreProcessor\Cache\Plugin;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class LessTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Css\PreProcessor\Cache\Plugin\Less
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
        $this->cacheManagerMock = $this->getMock(
            'Magento\Css\PreProcessor\Cache\CacheManager',
            array(),
            array(),
            '',
            false
        );
        $this->loggerMock = $this->getMock('Magento\Logger', array(), array(), '', false);
        $this->plugin = $this->objectManagerHelper->getObject(
            'Magento\Css\PreProcessor\Cache\Plugin\Less',
            array('cacheManager' => $this->cacheManagerMock, 'logger' => $this->loggerMock)
        );
    }

    /**
     * @param \Closure $proceed
     * @param $publisherFile
     * @param $targetDirectory
     * @param $cacheManagerData
     *
     * @dataProvider aroundProcessDataProvider
     */
    public function testAroundProcess(\Closure $proceed, $publisherFile, $targetDirectory, $cacheManagerData)
    {
        if (!empty($cacheManagerData)) {
            foreach ($cacheManagerData as $method => $info) {
                if ($method === 'getCachedFile') {
                    $this->cacheManagerMock->expects(
                        $this->once()
                    )->method(
                        $method
                    )->will(
                        $this->returnValue($info['result'])
                    );
                } else {
                    $this->cacheManagerMock->expects(
                        $this->once()
                    )->method(
                        $method
                    )->will(
                        $this->returnValue($info['result'])
                    );
                }
            }
        }
        $this->assertInstanceOf(
            'Magento\Framework\View\Publisher\CssFile',
            $this->plugin->aroundProcess(
                $this->getMock('\Magento\Css\PreProcessor\Less', array(), array(), '', false),
                $proceed,
                $publisherFile,
                $targetDirectory
            )
        );
    }

    /**
     * @return array
     */
    public function aroundProcessDataProvider()
    {
        $dir = 'targetDirectory';
        /**
         * Prepare first item
         */
        $cssFileFirst = $this->getMock('Magento\Framework\View\Publisher\CssFile', array(), array(), '', false);
        $cssFileFirst->expects($this->once())->method('getSourcePath')->will($this->returnValue(false));

        $expectedFirst = $this->getMock('Magento\Framework\View\Publisher\CssFile', array(), array(), '', false);
        $cssFileFirst->expects($this->once())->method('buildUniquePath')->will($this->returnValue('expectedFirst'));

        $invChainFirst = function (
            \Magento\Framework\View\Publisher\CssFile $subject,
            $directory
        ) use (
            $cssFileFirst,
            $dir,
            $expectedFirst
        ) {
            $this->assertEquals($subject, $cssFileFirst);
            $this->assertEquals($directory, $dir);
            return $expectedFirst;
        };

        /**
         * Prepare second item
         */
        $cssFileSecond = $this->getMock('Magento\Framework\View\Publisher\CssFile', array(), array(), '', false);
        $cssFileSecond->expects($this->once())->method('getSourcePath')->will($this->returnValue(false));

        $invChainSecond = function () {
            $this->fail('Incorrect call of procced method');
        };

        /**
         * Prepare third item
         */
        $cssFileThird = $this->getMock('Magento\Framework\View\Publisher\CssFile', array(), array(), '', false);
        $cssFileThird->expects($this->once())->method('getSourcePath')->will($this->returnValue(false));

        $expectedThird = $this->getMock('Magento\Framework\View\Publisher\CssFile', array(), array(), '', false);

        $invChainThird = function (
            \Magento\Framework\View\Publisher\CssFile $subject,
            $directory
        ) use (
            $cssFileThird,
            $dir,
            $expectedThird
        ) {
            $this->assertEquals($subject, $cssFileThird);
            $this->assertEquals($directory, $dir);
            return $expectedThird;
        };

        return array(
            'source path already exist' => array(
                'procced' => $invChainFirst,
                'publisherFile' => $cssFileFirst,
                'targetDirectory' => $dir,
                'cacheManagerData' => array(),
                'expected' => $expectedFirst
            ),
            'cached value exists' => array(
                'procced' => $invChainSecond,
                'publisherFile' => $cssFileSecond,
                'targetDirectory' => $dir,
                'cacheManagerData' => array('getCachedFile' => array('result' => $cssFileSecond)),
                'expected' => 'cached-value'
            ),
            'cached value does not exist' => array(
                'procced' => $invChainThird,
                'publisherFile' => $cssFileThird,
                'targetDirectory' => $dir,
                'cacheManagerData' => array(
                    'getCachedFile' => array('result' => null),
                    'saveCache' => array('result' => 'self')
                ),
                'expected' => $expectedThird
            )
        );
    }

    public function testAroundProcessException()
    {
        $dir = 'targetDirectory';
        $cssFile = $this->getMock('Magento\Framework\View\Publisher\CssFile', array(), array(), '', false);
        $cssFile->expects($this->once())->method('getSourcePath')->will($this->returnValue(false));

        $this->cacheManagerMock->expects($this->once())->method('getCachedFile')->will($this->returnValue(null));

        $exception = new \Magento\Framework\Filesystem\FilesystemException('Test Message');
        $proceed = function (
            \Magento\Framework\View\Publisher\CssFile $subject, $directory
        ) use ($cssFile, $dir, $exception) {
            $this->assertEquals($subject, $cssFile);
            $this->assertEquals($directory, $dir);
            throw $exception;
        };

        $this->loggerMock->expects(
            $this->once()
        )->method(
            'logException'
        )->with(
            $this->equalTo($exception)
        )->will(
            $this->returnSelf()
        );

        $this->assertNull(
            $this->plugin->aroundProcess(
                $this->getMock('\Magento\Css\PreProcessor\Less', array(), array(), '', false),
                $proceed,
                $cssFile,
                $dir
            )
        );
    }
}
