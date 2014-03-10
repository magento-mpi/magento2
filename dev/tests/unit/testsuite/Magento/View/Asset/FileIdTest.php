<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

class FileIdTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Asset\FileId
     */
    protected $_asset;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_pathGenerator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fileSource;

    public function setUp()
    {
        $this->_pathGenerator = $this->getMock('\Magento\View\Asset\PathGenerator', array(), array(), '', false );
        $this->_fileSource = $this->getMock('\Magento\View\Asset\SourceFileInterface', array(), array(), '', false );
        $this->_asset = new \Magento\View\Asset\FileId(
            $this->_pathGenerator,
            $this->_fileSource,
            'Module::fileId.ext',
            'baseUrl',
            'areaCode',
            'themePath',
            'localeCode'
        );
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage An extension is expected in file path: fileNoExt
     */
    public function testConstructorException()
    {
        new \Magento\View\Asset\FileId(
            $this->_pathGenerator,
            $this->_fileSource,
            'fileNoExt',
            'baseUrl',
            'areaCode',
            'themePath',
            'localeCode'
        );
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Unable to resolve the source file for '/fileId.ext'
     */
    public function testGetSourceFileException()
    {
        $this->_fileSource->expects($this->any())->method('getSourceFile')->will($this->returnValue(false));
        $this->_asset->getSourceFile();
    }

    public function testGetSourceFile()
    {
        $this->_fileSource->expects($this->once())->method('getSourceFile')->will($this->returnValue('fileSource'));

        $this->assertEquals('fileSource', $this->_asset->getSourceFile());

        //value is cached, no second call of fileSource
        $this->assertEquals('fileSource', $this->_asset->getSourceFile());
    }

    public function testGetRelativePath()
    {
        $this->_pathGenerator->expects($this->once())
            ->method('getPath')
            ->with('areaCode', 'themePath', 'localeCode', 'Module')
            ->will($this->returnValue('generatedPath'));
        $this->assertEquals('generatedPath/fileId.ext', $this->_asset->getRelativePath());
    }

    public function testGetModule()
    {
        $this->assertEquals('Module', $this->_asset->getModule());
    }

    public function testGetAreaCode()
    {
        $this->assertEquals('areaCode', $this->_asset->getAreaCode());
    }

    public function testThemePath()
    {
        $this->assertEquals('themePath', $this->_asset->getThemePath());
    }

    public function testGetLocaleCode()
    {
        $this->assertEquals('localeCode', $this->_asset->getLocaleCode());
    }

    public function testCreateSimilar()
    {
        $similarAsset = $this->_asset->createSimilar('Module::fileId.ext');
        $this->assertEquals($this->_asset, $similarAsset);
        $this->assertNotSame($this->_asset, $similarAsset);
    }

    /**
     * @param string $file
     * @param string $expectedErrorMessage
     * @dataProvider extractModuleExceptionDataProvider
     */
    public function testExtractModuleException($file, $expectedErrorMessage)
    {
        $this->setExpectedException('\Magento\Exception', $expectedErrorMessage);
        FileId::extractModule($file);
    }

    /**
     * @return array
     */
    public function extractModuleExceptionDataProvider()
    {
        return array(
            array('::no_scope.ext', 'Scope separator "::" cannot be used without scope identifier.'),
            array('../file.ext', 'File name \'../file.ext\' is forbidden for security reasons.'),
        );
    }

    public function testExtractModule()
    {
        $this->assertEquals(array('Module', 'File'), FileId::extractModule('Module::File'));
        $this->assertEquals(array('', 'File'), FileId::extractModule('File'));
        $this->assertEquals(
            array('Module', 'File::SomethingElse'),
            FileId::extractModule('Module::File::SomethingElse')
        );
    }
}
