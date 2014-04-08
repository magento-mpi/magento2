<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\FileResolution\Fallback\CacheData;

class GroupedTest extends \PHPUnit_Framework_TestCase
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
     * @var Grouped
     */
    private $object;

    protected function setUp()
    {
        $this->cache = $this->getMock(
            '\Magento\View\Design\FileResolution\Fallback\Cache', array(), array(), '', false
        );

        $this->theme = $this->getMockForAbstractClass('\Magento\View\Design\ThemeInterface');

        $this->object = new \Magento\View\Design\FileResolution\Fallback\CacheData\Grouped($this->cache);
    }

    /**
     * @param array $params
     * @param array $files
     *
     * @dataProvider getFromCacheDataProvider
     */
    public function testGetFromCache(array $params, array $files)
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

        $cachedSections = [
            'type:file|area:frontend|theme:magento_theme|locale:en_US' => [
                'module:Magento_Core|file:file.ext' => 'one/file.ext',
                'module:Magento_Core|file:other_file.ext' => 'one/other_file.ext',
                'module:_|file:file.ext' => 'two/file.ext',
                'module:_|file:other_file.ext' => 'two/other_file.ext',
            ],
            'type:file|area:frontend|theme:magento_theme|locale:' => [
                'module:Magento_Core|file:file.ext' => 'three/file.ext',
                'module:Magento_Core|file:other_file.ext' => 'four/other_file.ext',
            ],
            'type:file|area:frontend|theme:|locale:en_US' => [
                'module:Magento_Core|file:file.ext' => 'five/file.ext',
                'module:Magento_Core|file:other_file.ext' => 'five/other_file.ext',
            ],
            'type:file|area:|theme:magento_theme|locale:en_US' => [
                'module:Magento_Core|file:file.ext' => 'seven/file.ext',
                'module:Magento_Core|file:other_file.ext' => 'other_file.ext',
            ],
        ];

        $this->cache->expects($this->once())
            ->method('load')
            ->will($this->returnCallback(function ($sectionId) use ($cachedSections) {
                if (!isset($cachedSections[$sectionId])) {
                    return false;
                }
                return json_encode($cachedSections[$sectionId]);
            }));

        foreach ($files as $requested => $expected) {
            $actual = $this->object->getFromCache('file', $requested, $params);
            $this->assertSame($expected, $actual);
        }
    }

    /**
     * @return array
     */
    public function getFromCacheDataProvider()
    {
        return [
            'all params' => [
                [
                    'area' => 'frontend',
                    'theme' => 'magento_theme',
                    'locale' => 'en_US',
                    'namespace' => 'Magento',
                    'module' => 'Core',
                ],
                ['file.ext' => 'one/file.ext', 'other_file.ext' => 'one/other_file.ext'],
            ],
            'no area' => [
                [
                    'theme' => 'magento_theme',
                    'locale' => 'en_US',
                    'namespace' => 'Magento',
                    'module' => 'Core',
                ],
                ['file.ext' => 'seven/file.ext', 'other_file.ext' => 'other_file.ext'],
            ],
            'no theme' => [
                [
                    'area' => 'frontend',
                    'locale' => 'en_US',
                    'namespace' => 'Magento',
                    'module' => 'Core',
                ],
                ['file.ext' => 'five/file.ext', 'other_file.ext' => 'five/other_file.ext'],
            ],
            'no locale' => [
                [
                    'area' => 'frontend',
                    'theme' => 'magento_theme',
                    'namespace' => 'Magento',
                    'module' => 'Core',
                ],
                ['file.ext' => 'three/file.ext', 'other_file.ext' => 'four/other_file.ext'],
            ],
            'no namespace and module' => [
                [
                    'area' => 'frontend',
                    'theme' => 'magento_theme',
                    'locale' => 'en_US',
                ],
                ['file.ext' => 'two/file.ext', 'other_file.ext' => 'two/other_file.ext'],
            ],
        ];
    }

    /**
     * Verify that one and only one attempt to load cache is done even in case of cache absence
     */
    public function testGetFromCacheNothing()
    {
        $this->cache->expects($this->once())
            ->method('load');
        $params = [
            'area' => 'frontend',
            'locale' => 'en_US',
            'namespace' => 'Magento',
            'module' => 'Core',
        ];
        $this->assertFalse($this->object->getFromCache('type', 'file.ext', $params));
        $this->assertFalse($this->object->getFromCache('type', 'file.ext', $params));
    }

    /**
     * Ensure that cache is saved once and only once per section
     */
    public function testSaveToCache()
    {
        $paramsOne = [
            'area' => 'frontend',
            'locale' => 'en_US',
            'namespace' => 'Magento',
            'module' => 'Core',
        ];
        $paramsTwo = [
            'area' => 'backend',
            'locale' => 'en_US',
            'namespace' => 'Magento',
            'module' => 'Core',
        ];

        $this->cache->expects($this->exactly(2))
            ->method('save')
            ->will($this->returnValueMap([
                [
                    json_encode([
                        'module:Magento_Core|file:file.ext' => 'some/file.ext',
                        'module:Magento_Core|file:other_file.ext' => 'some/other_file.ext',
                    ]),
                    'type:file|area:frontend|theme:|locale:en_US',
                    true,
                ],
                [
                    json_encode(['module:Magento_Core|file:file.ext' => 'some/other/file.ext']),
                    'type:view|area:backend|theme:|locale:en_US',
                    true,
                ],
            ]));


        $this->object->saveToCache('some/file.ext', 'file', 'file.ext', $paramsOne);
        $this->object->saveToCache('some/other_file.ext', 'file', 'other_file.ext', $paramsOne);
        $this->object->saveToCache('some/other/file.ext', 'view', 'file.ext', $paramsTwo);

        $this->object = null;
    }

    /**
     * Verify that no attempt to save cache is done, when nothing is updated
     */
    public function testSaveToCacheNothing()
    {
        $this->cache->expects($this->never())
            ->method('save');
        $this->object = null;
    }
}
