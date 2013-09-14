<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test that Design Package delegates fallback resolution to a Fallback model
 */
class Magento_Core_Model_Design_FileResolution_Strategy_FallbackTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fallbackFactory;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fallbackFile;

    /*
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fallbackLocale;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fallbackViewFile;

    /**
     * @var \Magento\Core\Model\Theme
     */
    protected $_theme;

    public function setUp()
    {
        $this->_fallbackFile = $this->getMockForAbstractClass('Magento\Core\Model\Design\Fallback\Rule\RuleInterface');
        $this->_fallbackLocale = $this->getMockForAbstractClass(
            'Magento\Core\Model\Design\Fallback\Rule\RuleInterface'
        );
        $this->_fallbackViewFile = $this->getMockForAbstractClass(
            'Magento\Core\Model\Design\Fallback\Rule\RuleInterface'
        );

        $this->_fallbackFactory = $this->getMock(
            'Magento\Core\Model\Design\Fallback\Factory',
            array('createLocaleFileRule', 'createFileRule', 'createViewFileRule'),
            array($this->getMock('Magento\Core\Model\Dir', array(), array(), '', false))
        );
        $this->_fallbackFactory
            ->expects($this->any())->method('createLocaleFileRule')->will($this->returnValue($this->_fallbackLocale));
        $this->_fallbackFactory
            ->expects($this->any())->method('createFileRule')->will($this->returnValue($this->_fallbackFile));
        $this->_fallbackFactory
            ->expects($this->any())->method('createViewFileRule')->will($this->returnValue($this->_fallbackViewFile));

        $this->_theme = $this->getMock('Magento\Core\Model\Theme', array(), array(), '', false);
    }

    public function tearDown()
    {
        $this->_fallbackFactory = null;
        $this->_fallbackFile = null;
        $this->_fallbackLocale = null;
        $this->_fallbackViewFile = null;
        $this->_theme = null;
    }

    /**
     * @dataProvider getFileDataProvider
     */
    public function testGetFile($fullModuleName, $namespace, $module, $targetFile, $expectedFileName)
    {
        $filesystem = $this->_getFileSystemMock($targetFile);

        $fallback = new \Magento\Core\Model\Design\FileResolution\Strategy\Fallback(
            $filesystem, $this->_fallbackFactory
        );

        $params = array('area' => 'area', 'theme' => $this->_theme, 'namespace' => $namespace, 'module' => $module);

        $this->_fallbackFile->expects($this->once())
            ->method('getPatternDirs')
            ->with($params)
            ->will($this->returnValue(array('found_folder', 'not_found_folder')));

        $filename = $fallback->getFile('area', $this->_theme, 'file.txt', $fullModuleName);

        $this->assertEquals(str_replace('/', DIRECTORY_SEPARATOR, $expectedFileName), $filename);
    }

    public function getFileDataProvider()
    {
        return array(
            'no module, file found' => array(
                null,
                null,
                null,
                'found_folder/file.txt',
                'found_folder/file.txt',
            ),
            'module, file found' => array(
                'Namespace_Module',
                'Namespace',
                'Module',
                'found_folder/file.txt',
                'found_folder/file.txt',
            ),
            'no module, file not found' => array(
                null,
                null,
                null,
                null,
                'not_found_folder/file.txt',
            ),
            'module, file not found' => array(
                'Namespace_Module',
                'Namespace',
                'Module',
                null,
                'not_found_folder/file.txt',
            ),
        );
    }

    /**
     * @dataProvider getLocaleFileDataProvider
     */
    public function testGetLocaleFile($targetFile, $expectedFileName)
    {
        $filesystem = $this->_getFileSystemMock($targetFile);

        $fallback = new \Magento\Core\Model\Design\FileResolution\Strategy\Fallback(
            $filesystem, $this->_fallbackFactory
        );

        $params = array('area' => 'area', 'theme' => $this->_theme, 'locale' => 'locale');

        $this->_fallbackLocale->expects($this->once())
            ->method('getPatternDirs')
            ->with($params)
            ->will($this->returnValue(array('found_folder', 'not_found_folder')));

        $filename = $fallback->getLocaleFile('area', $this->_theme, 'locale', 'file.txt');

        $this->assertEquals(str_replace('/', DIRECTORY_SEPARATOR, $expectedFileName), $filename);
    }

    public function getLocaleFileDataProvider()
    {
        return array(
            'file found' => array(
                'found_folder/file.txt',
                'found_folder/file.txt',
            ),
            'file not found' => array(
                null,
                'not_found_folder/file.txt',
            )
        );
    }

    /**
     * @dataProvider getFileDataProvider
     */
    public function testGetViewFile($fullModuleName, $namespace, $module, $targetFile, $expectedFileName)
    {
        $filesystem = $this->_getFileSystemMock($targetFile);

        $fallback = new \Magento\Core\Model\Design\FileResolution\Strategy\Fallback(
            $filesystem,
            $this->_fallbackFactory
        );

        $params = array('area' => 'area', 'theme' => $this->_theme, 'namespace' => $namespace, 'module' => $module,
            'locale' => 'locale');

        $this->_fallbackViewFile->expects($this->once())
            ->method('getPatternDirs')
            ->with($params)
            ->will($this->returnValue(array('found_folder', 'not_found_folder')));

        $filename = $fallback->getViewFile('area', $this->_theme, 'locale', 'file.txt', $fullModuleName);

        $this->assertEquals(str_replace('/', DIRECTORY_SEPARATOR, $expectedFileName), $filename);
    }

    /**
     * @param string $targetFile
     * @return \Magento\Filesystem|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getFileSystemMock($targetFile)
    {
        $targetFile = str_replace('/', DIRECTORY_SEPARATOR, $targetFile);
        /** @var $filesystem \Magento\Filesystem */
        $filesystem = $this->getMock('Magento\Filesystem', array('has'), array(), '', false);
        $filesystem->expects($this->any())
            ->method('has')
            ->will($this->returnCallback(
                function ($tryFile) use ($targetFile) {
                    return ($tryFile == $targetFile);
                }
        ));

        return $filesystem;
    }
}
