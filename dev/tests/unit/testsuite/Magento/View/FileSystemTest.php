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
     * @var \Magento\View\Design\FileResolution\Fallback|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_viewFileResolution;

    /**
     * @var \Magento\View\Asset\Service|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_assetService;


    protected function setUp()
    {
        $this->_viewFileResolution = $this->getMock('Magento\View\Design\FileResolution\Fallback', array(),
            array(), '', false
        );
        $this->_assetService = $this->getMock('Magento\View\Asset\Service',
            array('extractScope', 'updateDesignParams', 'createAsset'), array(), '', false
        );

        $this->_model = new \Magento\View\FileSystem($this->_viewFileResolution, $this->_assetService);
    }

    public function testGetFilename()
    {
        $params = array(
            'area'       => 'some_area',
            'themeModel' => $this->getMock('Magento\View\Design\ThemeInterface', array(), array(), '', false, false),
            'module'     => 'Some_Module'   //It should be set in \Magento\View\Asset\Service::extractScope
                                            // but PHPUnit has problems with passing arguments by reference
        );
        $file = 'Some_Module::some_file.ext';
        $expected = 'path/to/some_file.ext';

        $this->_viewFileResolution->expects($this->once())
            ->method('getFile')
            ->with($params['area'], $params['themeModel'], 'some_file.ext', 'Some_Module')
            ->will($this->returnValue($expected));

        $this->_assetService->expects($this->any())
            ->method('extractScope')
            ->with($file, $params)
            ->will($this->returnValue('some_file.ext'));

        $actual = $this->_model->getFilename($file, $params);
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

        $this->_viewFileResolution->expects($this->once())
            ->method('getLocaleFile')
            ->with($params['area'], $params['themeModel'], $params['locale'], 'some_file.ext')
            ->will($this->returnValue($expected));

        $actual = $this->_model->getLocaleFileName($file, $params);
        $this->assertEquals($expected, $actual);
    }

    public function testGetViewFile()
    {
        $asset = $this->getMockForAbstractClass('\Magento\View\Asset\LocalInterface');
        $asset->expects($this->once())->method('getSourceFile')->will($this->returnValue('/source/file'));
        $this->_assetService->expects($this->once())
            ->method('createAsset')
            ->with('file', array())
            ->will($this->returnValue($asset));
        $this->assertEquals('/source/file', $this->_model->getViewFile('file'));
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
        );
    }
}
