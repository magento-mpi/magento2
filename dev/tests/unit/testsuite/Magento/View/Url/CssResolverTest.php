<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Url;

class CssResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Url\CssResolver
     */
    protected $object;

    protected function setUp()
    {
        $filesystem = $this->getMock('Magento\App\Filesystem', array('getPath', '__wakeup'), array(), '', false);
        $filesystem->expects($this->any())
            ->method('getPath')
            ->with(\Magento\App\Filesystem::ROOT_DIR)
            ->will($this->returnValue('/base_dir/'));
        $viewFilesystem = $this->getMock('Magento\View\Filesystem', array('normalizePath'), array(), '', false);
        $viewFilesystem->expects($this->any())
            ->method('normalizePath')
            ->will($this->returnValueMap(array(
                array(
                    '/does/not/matter.css',
                    '/does/not/matter.css'
                ),
                array(
                    '/base_dir/pub/assets/new/location/any_new_name.css',
                    '/base_dir/pub/assets/new/location/any_new_name.css'
                ),
                array(
                    '/base_dir\pub/assets\new/location/any_new_name.css',
                    '/base_dir\pub/assets\new/location/any_new_name.css'
                ),
                array(
                    '/base_dir/pub/assets/referenced/di/any_new_name.css',
                    '/base_dir/pub/assets/referenced/di/any_new_name.css'
                ),
                array(
                    '/base_dir/pub/any_new_name.css',
                    '/base_dir/pub/any_new_name.css'
                ),
                array(
                    '/not/base_dir/pub/new/file.css',
                    '/not/base_dir/pub/new/file.css'
                ),
                array(
                    '/base_dir/pub/css/file.css',
                    '/base_dir/pub/css/file.css'
                ),
                array(
                    '/not/base_dir/pub/css/file.css',
                    '/not/base_dir/pub/css/file.css'
                ),
                array(
                    '/base_dir/pub/new/file.css',
                    '/base_dir/pub/new/file.css'
                ),
                array(
                    '/base_dir/pub/assets/referenced/dir/../images/h2.gif',
                    '/base_dir/pub/assets/referenced/images/h2.gif'
                ),
                array(
                    '/base_dir/pub/assets/referenced/dir/Magento_Theme::favicon.ico',
                    '/base_dir/pub/assets/referenced/dir/Magento_Theme::favicon.ico'
                ),
                array(
                    '/base_dir/pub/assets/referenced/dir/original.css',
                    '/base_dir/pub/assets/referenced/dir/original.css'
                ),
                array(
                    '/base_dir/pub/assets/referenced/dir/body.gif',
                    '/base_dir/pub/assets/referenced/dir/body.gif'
                ),
                array(
                    '/base_dir/pub/dir/body.gif',
                    '/base_dir/pub/dir/body.gif'
                ),
                array(
                    '/base_dir/pub/css/body.gif',
                    '/base_dir/pub/css/body.gif'
                ),
                array(
                    '/not/base_dir/pub/css/body.gif',
                    '/not/base_dir/pub/css/body.gif'
                )
            )));
        $this->object = new CssResolver($filesystem, $viewFilesystem);
    }

    /**
     * @param string $cssContent
     * @param string $originalPath
     * @param string $newPath
     * @param callable $callback
     * @param string $expected
     * @dataProvider replaceRelativeUrlsDataProvider
     */
    public function testReplaceRelativeUrls($cssContent, $originalPath, $newPath, $callback, $expected)
    {
        $this->markTestIncomplete(
            'MAGETWO-21654. Major part of this logic moved to \Magento\View\Asset\PreProcessor\ModuleNotation'
        );
        $actual = $this->object->replaceRelativeUrls($cssContent, $originalPath, $newPath, $callback);
        $this->assertEquals($expected, $actual);
    }

    public static function replaceRelativeUrlsDataProvider()
    {
        $fixturePath = __DIR__ . '/_files/';
        $callback = function ($relativeUrl) {
            return '/base_dir/pub/assets/referenced/dir/' . $relativeUrl;
        };

        $object = new \Magento\Object(array('resolved_path' => array('body.gif' => '/base_dir/pub/dir/body.gif')));
        $objectCallback = array($object, 'getResolvedPath');

        $source = file_get_contents($fixturePath . 'source.css');
        $result = file_get_contents($fixturePath . 'result.css');

        return array(
            'standard parsing' => array(
                $source,
                '/does/not/matter.css',
                '/base_dir/pub/assets/new/location/any_new_name.css',
                $callback,
                $result,
            ),
            'back slashes in new name' => array(
                $source,
                '/does/not/matter.css',
                '/base_dir\pub/assets\new/location/any_new_name.css',
                $callback,
                $result,
            ),
            'directory with subset name' => array(
                'body {background: url(body.gif);}',
                '/base_dir/pub/assets/referenced/dir/original.css',
                '/base_dir/pub/assets/referenced/di/any_new_name.css',
                null,
                'body {background: url(../dir/body.gif);}',
            ),
            'objectCallback' => array(
                'body {background: url(body.gif);}',
                '/does/not/matter.css',
                '/base_dir/pub/any_new_name.css',
                $objectCallback,
                'body {background: url(dir/body.gif);}',
            ),
        );
    }
}
