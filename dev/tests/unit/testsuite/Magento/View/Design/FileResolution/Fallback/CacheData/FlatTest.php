<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\FileResolution\Fallback\CacheData;

class FlatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Design\FileResolution\Fallback\Cache|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cache;

    /**
     * @var \Magento\View\Design\ThemeInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $theme;

    /**
     * @var Flat
     */
    private $object;

    protected function setUp()
    {
        $this->cache = $this->getMock(
            '\Magento\View\Design\FileResolution\Fallback\Cache', array(), array(), '', false
        );

        $this->theme = $this->getMockForAbstractClass('\Magento\View\Design\ThemeInterface');

        $this->object = new \Magento\View\Design\FileResolution\Fallback\CacheData\Flat($this->cache);
    }

    /**
     * @param array $params
     * @param string $expectedId
     * @param string $expectedValue
     *
     * @dataProvider cacheDataProvider
     */
    public function testGetFromCache(array $params, $expectedId, $expectedValue)
    {
        if (isset($params['theme'])) {
            $this->theme->expects($this->any())
                ->method('getThemePath')
                ->will($this->returnValue($params['theme']));
            $params['theme'] = $this->theme;
        } else {
            $this->theme->expects($this->never())
                ->method('getThemePath');
        }

        $this->cache->expects($this->once())
            ->method('load')
            ->with($expectedId)
            ->will($this->returnValue($expectedValue));

        $actual = $this->object->getFromCache('file', 'file.ext', $params);
        $this->assertSame($expectedValue, $actual);
    }

    /**
     * @param array $params
     * @param string $expectedId
     * @param string $savedValue
     *
     * @dataProvider cacheDataProvider
     */
    public function testSaveToCache(array $params, $expectedId, $savedValue)
    {
        if (isset($params['theme'])) {
            $this->theme->expects($this->any())
                ->method('getThemePath')
                ->will($this->returnValue($params['theme']));
            $params['theme'] = $this->theme;
        } else {
            $this->theme->expects($this->never())
                ->method('getThemePath');
        }

        $this->cache->expects($this->once())
            ->method('save')
            ->with($savedValue, $expectedId)
            ->will($this->returnValue(true));

        $actual = $this->object->saveToCache($savedValue, 'file', 'file.ext', $params);
        $this->assertTrue($actual);
    }

    /**
     * @return array
     */
    public function cacheDataProvider()
    {
        return [
            'all params' => [
                [
                    'area' => 'frontend',
                    'theme' => 'magento_theme',
                    'locale' => 'en_US',
                    'namespace' => 'Magento',
                    'module' => 'Module',
                ],
                'type:file|area:frontend|theme:magento_theme|locale:en_US|module:Magento_Module|file:file.ext',
                'one/file.ext',
            ],
            'no area' => [
                [
                    'theme' => 'magento_theme',
                    'locale' => 'en_US',
                    'namespace' => 'Magento',
                    'module' => 'Module',
                ],
                'type:file|area:|theme:magento_theme|locale:en_US|module:Magento_Module|file:file.ext',
                'two/file.ext',
            ],
            'no theme' => [
                [
                    'area' => 'frontend',
                    'locale' => 'en_US',
                    'namespace' => 'Magento',
                    'module' => 'Module',
                ],
                'type:file|area:frontend|theme:|locale:en_US|module:Magento_Module|file:file.ext',
                'three/file.ext',
            ],
            'no locale' => [
                [
                    'area' => 'frontend',
                    'theme' => 'magento_theme',
                    'namespace' => 'Magento',
                    'module' => 'Module',
                ],
                'type:file|area:frontend|theme:magento_theme|locale:|module:Magento_Module|file:file.ext',
                'four/file.ext',
            ],
            'no namespace and module' => [
                [
                    'area' => 'frontend',
                    'theme' => 'magento_theme',
                    'locale' => 'en_US',
                ],
                'type:file|area:frontend|theme:magento_theme|locale:en_US|module:_|file:file.ext',
                'five/file.ext',
            ],
        ];
    }
}
