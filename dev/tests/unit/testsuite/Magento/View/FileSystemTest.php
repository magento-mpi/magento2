<?php
/**
 * {license_notice}
 *
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for view filesystem model
 */
namespace Magento\View;

class FileSystemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\FileSystem
     */
    protected $_model;

    /**
     * @var \Magento\View\Design\FileResolution\Fallback\File|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fileResolution;

    /**
     * @var \Magento\View\Design\FileResolution\Fallback\TemplateFile|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_templateFileResolution;

    /**
     * @var \Magento\View\Design\FileResolution\Fallback\LocaleFile|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_localeFileResolution;

    /**
     * @var \Magento\View\Design\FileResolution\Fallback\StaticFile|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_staticFileResolution;

    /**
     * @var \Magento\View\Asset\Repository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_assetRepo;

    protected function setUp()
    {
        $this->_fileResolution = $this->getMock('Magento\View\Design\FileResolution\Fallback\File', array(),
            array(), '', false
        );
        $this->_templateFileResolution = $this->getMock(
            'Magento\View\Design\FileResolution\Fallback\TemplateFile', array(), array(), '', false
        );
        $this->_localeFileResolution = $this->getMock('Magento\View\Design\FileResolution\Fallback\LocaleFile', array(),
            array(), '', false
        );
        $this->_staticFileResolution = $this->getMock('Magento\View\Design\FileResolution\Fallback\StaticFile', array(),
            array(), '', false
        );
        $this->_assetRepo = $this->getMock('Magento\View\Asset\Repository',
            array('extractScope', 'updateDesignParams', 'createAsset'), array(), '', false
        );

        $this->_model = new \Magento\View\FileSystem(
            $this->_fileResolution,
            $this->_templateFileResolution,
            $this->_localeFileResolution,
            $this->_staticFileResolution,
            $this->_assetRepo
        );
    }

    public function testGetFilename()
    {
        $params = array(
            'area'       => 'some_area',
            'themeModel' => $this->getMock('Magento\View\Design\ThemeInterface', array(), array(), '', false, false),
            'module'     => 'Some_Module'   //It should be set in \Magento\View\Asset\Repository::extractScope
                                            // but PHPUnit has troubles with passing arguments by reference
        );
        $file = 'Some_Module::some_file.ext';
        $expected = 'path/to/some_file.ext';

        $this->_fileResolution->expects($this->once())
            ->method('getFile')
            ->with($params['area'], $params['themeModel'], 'some_file.ext', 'Some_Module')
            ->will($this->returnValue($expected));

        $this->_assetRepo->expects($this->any())
            ->method('extractScope')
            ->with($file, $params)
            ->will($this->returnValue('some_file.ext'));

        $actual = $this->_model->getFilename($file, $params);
        $this->assertEquals($expected, $actual);
    }

    public function testGetTemplateFileName()
    {
        $params = array(
            'area'       => 'some_area',
            'themeModel' => $this->getMock('Magento\View\Design\ThemeInterface', array(), array(), '', false, false),
            'module'     => 'Some_Module'   //It should be set in \Magento\View\Asset\Repository::extractScope
                                            // but PHPUnit has troubles with passing arguments by reference
        );
        $file = 'Some_Module::some_file.ext';
        $expected = 'path/to/some_file.ext';

        $this->_templateFileResolution->expects($this->once())
            ->method('getFile')
            ->with($params['area'], $params['themeModel'], 'some_file.ext', 'Some_Module')
            ->will($this->returnValue($expected));

        $this->_assetRepo->expects($this->any())
            ->method('extractScope')
            ->with($file, $params)
            ->will($this->returnValue('some_file.ext'));

        $actual = $this->_model->getTemplateFileName($file, $params);
        $this->assertEquals($expected, $actual);
    }

    public function testGetLocaleFileName()
    {
        $params = array(
            'area' => 'some_area',
            'themeModel' => $this->getMock('Magento\View\Design\ThemeInterface', array(), array(), '', false, false),
            'locale' => 'some_locale'
        );
        $file = 'some_file.ext';
        $expected = 'path/to/some_file.ext';

        $this->_localeFileResolution->expects($this->once())
            ->method('getFile')
            ->with($params['area'], $params['themeModel'], $params['locale'], 'some_file.ext')
            ->will($this->returnValue($expected));

        $actual = $this->_model->getLocaleFileName($file, $params);
        $this->assertEquals($expected, $actual);
    }

    public function testGetViewFile()
    {
        $params = array(
            'area' => 'some_area',
            'themeModel' => $this->getMock('Magento\View\Design\ThemeInterface', array(), array(), '', false, false),
            'locale' => 'some_locale'
        );
        $file = 'Some_Module::some_file.ext';
        $expected = 'path/to/some_file.ext';

        $this->_staticFileResolution->expects($this->once())
            ->method('getFile')
            ->with($params['area'], $params['themeModel'], $params['locale'], 'some_file.ext', 'Some_Module')
            ->will($this->returnValue($expected));

        $actual = $this->_model->getStaticFileName($file, $params);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @param string $path
     * @param string $expectedResult
     * @dataProvider normalizePathDataProvider
     */
    public function testNormalizePath($path, $expectedResult)
    {
        $result = $this->_model->normalizePath($path);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function normalizePathDataProvider()
    {
        return array(
            'standard path' => array(
                '/dir/somedir/somefile.ext',
                '/dir/somedir/somefile.ext'
            ),
            'one dot path' => array(
                '/dir/somedir/./somefile.ext',
                '/dir/somedir/somefile.ext',
            ),
            'two dots path' => array(
                '/dir/somedir/../somefile.ext',
                '/dir/somefile.ext',
            ),
            'two times two dots path' => array(
                '/dir/../somedir/../somefile.ext',
                '/somefile.ext',
            ),
        );
    }

    /**
     * @param string $relatedPath
     * @param string $path
     * @param string $expectedResult
     * @dataProvider offsetPathDataProvider
     */
    public function testOffsetPath($relatedPath, $path, $expectedResult)
    {
        $result = $this->_model->offsetPath($relatedPath, $path);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function offsetPathDataProvider()
    {
        return array(
            'local path' => array(
                '/some/directory/two/another/file.ext',
                '/some/directory/one/file.ext',
                '../two/another'
            ),
            'local path reverted' => array(
                '/some/directory/one/file.ext',
                '/some/directory/two/another/file.ext',
                '../../one'
            ),
            'url' => array(
                'http://example.com/images/logo.gif',
                'http://example.com/themes/demo/css/styles.css',
                '../../../images'
            ),
            'same path' => array(
                '/some/directory/file.ext',
                '/some/directory/file1.ext',
                '.'
            ),
            'non-normalized' => array(
                '/some/directory/../one/file.ext',
                '/some/directory/./two/another/file.ext',
                '../../../one'
            ),
        );
    }
}
