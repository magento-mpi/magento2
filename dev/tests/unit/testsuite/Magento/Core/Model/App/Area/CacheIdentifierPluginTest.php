<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\App\Area;

/**
 * Class CacheIdentifierPluginTest
 * Test for plugin to identifier to work with design exceptions
 *
 * @package Magento\Core\Model\App\Area
 */
class CacheIdentifierPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\App\Area\CacheIdentifierPlugin
     */
    protected $plugin;

    /**
     * @var \Magento\Core\Model\App\Area\DesignExceptions
     */
    protected $designExceptionsMock;

    /**
     * @var \Magento\App\Request\Http
     */
    protected $requestMock;

    /**
     * @var \Magento\PageCache\Model\Config
     */
    protected $pageCacheConfigMock;

    /**
     * Set up data for test
     */
    public function setUp()
    {
        $this->designExceptionsMock = $this->getMock(
            'Magento\Core\Model\App\Area\DesignExceptions',
            ['getThemeForUserAgent'],
            [],
            '',
            false
        );
        $this->requestMock = $this->getMock('Magento\App\Request\Http', [], [], '', false);
        $this->pageCacheConfigMock = $this->getMock(
            'Magento\PageCache\Model\Config',
            ['getType', 'isEnabled'],
            [],
            '',
            false
        );

        $this->plugin = new \Magento\Core\Model\App\Area\CacheIdentifierPlugin(
            $this->designExceptionsMock,
            $this->requestMock,
            $this->pageCacheConfigMock
        );
    }

    /**
     * Test of adding design exceptions to the kay of cache hash
     *
     * @param string $cacheType
     * @param bool $isPageCacheEnabled
     * @param string|false $result
     * @param string $uaException
     * @param string $expected
     * @dataProvider testAfterGetValueDataProvider
     */
    public function testAfterGetValue($cacheType, $isPageCacheEnabled, $result, $uaException, $expected)
    {
        $identifierMock = $this->getMock('Magento\App\PageCache\Identifier', [], [], '', false);

        $this->pageCacheConfigMock->expects($this->once())
            ->method('getType')
            ->will($this->returnValue($cacheType));
        $this->pageCacheConfigMock->expects($this->any())
            ->method('isEnabled')
            ->will($this->returnValue($isPageCacheEnabled));
        $this->designExceptionsMock->expects($this->any())
            ->method('getThemeForUserAgent')
            ->will($this->returnValue($uaException));

        $this->assertEquals($expected, $this->plugin->afterGetValue($identifierMock, $result));
    }

    /**
     * Data provider for testAfterGetValue
     *
     * @return array
     */
    public function testAfterGetValueDataProvider()
    {
        return [
            'Varnish + PageCache enabled' => [\Magento\PageCache\Model\Config::VARNISH, true, null, false, false],
            'Built-in + PageCache disabled' => [\Magento\PageCache\Model\Config::BUILT_IN, false, null, false, false],
            'Built-in + PageCache enabled' => [\Magento\PageCache\Model\Config::BUILT_IN, true, null, false, false],
            'Built-in, PageCache enabled, no user-agent exceptions' =>
                [\Magento\PageCache\Model\Config::BUILT_IN, true, 'aa123aa', false, 'aa123aa'],
            'Built-in, PageCache enabled, with design exception' =>
                [\Magento\PageCache\Model\Config::BUILT_IN, true, 'aa123aa', '7', '7aa123aa']
        ];
    }
}
