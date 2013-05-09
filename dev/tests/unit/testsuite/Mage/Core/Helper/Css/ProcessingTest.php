<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Helper_Css_ProcessingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Helper_Css_Processing
     */
    protected $_object;

    public function setUp()
    {
        $filesystem = new Magento_Filesystem(new Magento_Filesystem_Adapter_Local());
        $dirs = new Mage_Core_Model_Dir('/base_dir');
        $this->_object = new Mage_Core_Helper_Css_Processing($filesystem, $dirs);
    }

    /**
     * @param string $cssContent
     * @param string $cssFilePath
     * @param callable $callback
     * @param string $expected
     * @dataProvider replaceCssRelativeUrlsDataProvider
     */
    public function testReplaceCssRelativeUrls($cssContent, $cssFilePath, $callback, $expected)
    {
        $actual = $this->_object->replaceCssRelativeUrls($cssContent, $cssFilePath, $callback);
        $this->assertEquals($expected, $actual);
    }

    public static function replaceCssRelativeUrlsDataProvider()
    {
        $fixturePath = __DIR__ . '/_files/';
        $callback = function ($relativeUrl) {
            return '/base_dir/pub/assets/referenced/dir/' . $relativeUrl;
        };
        $callbackWindows = function ($relativeUrl) {
            return '/base_dir/pub\assets/referenced\dir/' . $relativeUrl;
        };

        $object = new Varien_Object(array('resolved_path' => array('body.gif' => '/base_dir/pub/dir/body.gif')));
        $objectCallback = array($object, 'getResolvedPath');

        $source = file_get_contents($fixturePath . 'source.css');
        $result = file_get_contents($fixturePath . 'result.css');

        return array(
            'standard parsing' => array(
                $source,
                '/base_dir/pub/assets/new/location/any_new_name.css',
                $callback,
                $result,
            ),
            'Windows slashes in new name' => array(
                $source,
                '/base_dir\pub/assets\new/location/any_new_name.css',
                $callback,
                $result,
            ),
            'Windows slashes in referenced name' => array(
                $source,
                '/base_dir/pub/assets/new/location/any_new_name.css',
                $callbackWindows,
                $result,
            ),
            'same directory' => array(
                $source,
                '/base_dir/pub/assets/referenced/dir/any_new_name.css',
                $callback,
                $source,
            ),
            'directory with superset name' => array(
                'body {background: url(body.gif);}',
                '/base_dir/pub/assets/referenced/dirname/any_new_name.css',
                $callback,
                'body {background: url(../dir/body.gif);}',
            ),
            'directory with subset name' => array(
                'body {background: url(body.gif);}',
                '/base_dir/pub/assets/referenced/di/any_new_name.css',
                $callback,
                'body {background: url(../dir/body.gif);}',
            ),
            'objectCallback' => array(
                'body {background: url(body.gif);}',
                '/base_dir/pub/any_new_name.css',
                $objectCallback,
                'body {background: url(dir/body.gif);}',
            ),
        );
    }

    /**
     * @param string $cssFilePath
     * @param string $referencedFilePath
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage Offset can be calculated for internal resources only.
     * @dataProvider replaceCssRelativeUrlsExceptionDataProvider
     */
    public function testReplaceCssRelativeUrlsException($cssFilePath, $referencedFilePath)
    {
        $callback = function() use ($referencedFilePath) {
            return $referencedFilePath;
        };

        $this->_object->replaceCssRelativeUrls('body {background: url(body.gif);}', $cssFilePath, $callback);
    }

    /**
     * @return array
     */
    public static function replaceCssRelativeUrlsExceptionDataProvider()
    {
        return array(
            'css path is out of reach' => array(
                '/not/base_dir/pub/css/file.css',
                '/base_dir/pub/referenced/body.gif',
            ),
            'referenced path is out of reach' => array(
                '/base_dir/pub/css/file.css',
                '/not/base_dir/pub/referenced/body.gif',
            ),
        );
    }
}
