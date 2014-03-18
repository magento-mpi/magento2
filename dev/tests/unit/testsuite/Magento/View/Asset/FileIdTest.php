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
        $this->_asset = $this->createAsset('Fixture_Module::fileId.ext');
    }

    /**
     * Return newly created asset instance
     *
     * @param string $fileId
     * @param string $baseUrl
     * @param string $areaCode
     * @param string $themePath
     * @param string $localeCode
     * @return FileId
     */
    protected function createAsset(
        $fileId,
        $baseUrl = 'http://127.0.0.1',
        $areaCode = 'fixture_area',
        $themePath = 'fixture_theme',
        $localeCode = 'fixture_locale'
    ) {
        return new FileId(
            $this->_pathGenerator, $this->_fileSource, $fileId, $baseUrl, $areaCode, $themePath, $localeCode
        );
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage An extension is expected in file path: fileNoExt
     */
    public function testConstructorException()
    {
        $this->createAsset('fileNoExt');
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
            ->with('fixture_area', 'fixture_theme', 'fixture_locale', 'Fixture_Module')
            ->will($this->returnValue('generatedPath'));
        $this->assertEquals('generatedPath/fileId.ext', $this->_asset->getRelativePath());
    }

    public function testGetModule()
    {
        $this->assertEquals('Fixture_Module', $this->_asset->getModule());
    }

    public function testGetAreaCode()
    {
        $this->assertEquals('fixture_area', $this->_asset->getAreaCode());
    }

    public function testThemePath()
    {
        $this->assertEquals('fixture_theme', $this->_asset->getThemePath());
    }

    public function testGetLocaleCode()
    {
        $this->assertEquals('fixture_locale', $this->_asset->getLocaleCode());
    }

    /**
     * @param string $fixtureFileId
     * @param string $relativeFileId
     * @param array $expectedAssetData
     *
     * @dataProvider createRelativeDataProvider
     */
    public function testCreateRelative(
        $fixtureFileId, $relativeFileId, array $expectedAssetData
    ) {
        $fixtureAsset = $this->createAsset($fixtureFileId);
        $expectedAssetData['areaCode'] = $fixtureAsset->getAreaCode();
        $expectedAssetData['themePath'] = $fixtureAsset->getThemePath();
        $expectedAssetData['localeCode'] = $fixtureAsset->getLocaleCode();

        $actualResult = $fixtureAsset->createRelative($relativeFileId);
        $actualAssetData = $this->retrieveDataViaGetters($actualResult, array_keys($expectedAssetData));

        $this->assertEquals($expectedAssetData, $actualAssetData);
        $this->assertNotSame($fixtureAsset, $actualResult);
    }

    /**
     * @return array
     */
    public function createRelativeDataProvider()
    {
        return array(
            'absolute modular file id' => array(
                'Fixture_ModuleOne::fixture_dir/fileId.ext',
                'Fixture_ModuleTwo::anotherFileId.ext',
                array(
                    'relativePath'  => '/anotherFileId.ext',
                    'module'        => 'Fixture_ModuleTwo',
                    'url'           => 'http://127.0.0.1/anotherFileId.ext',
                    'contentType'   => 'ext',
                    'filePath'      => 'anotherFileId.ext',
                )
            ),
            'relative to modular file id' => array(
                'Fixture_Module::fixture_dir/fileId.ext',
                'relative/path/anotherFileId.ext',
                array(
                    'relativePath'  => '/fixture_dir/relative/path/anotherFileId.ext',
                    'module'        => 'Fixture_Module',
                    'url'           => 'http://127.0.0.1/fixture_dir/relative/path/anotherFileId.ext',
                    'contentType'   => 'ext',
                    'filePath'      => 'fixture_dir/relative/path/anotherFileId.ext',
                )
            ),
            'relative to non-modular file id' => array(
                'fixture_dir/fileId.ext',
                'relative/path/anotherFileId.ext',
                array(
                    'relativePath'  => '/fixture_dir/relative/path/anotherFileId.ext',
                    'module'        => '',
                    'url'           => 'http://127.0.0.1/fixture_dir/relative/path/anotherFileId.ext',
                    'contentType'   => 'ext',
                    'filePath'      => 'fixture_dir/relative/path/anotherFileId.ext',
                )
            ),
        );
    }

    /**
     * Retrieve values of object properties by calling corresponding conventionally named getter methods
     *
     * @param object $object
     * @param array $properties
     * @return array
     */
    protected function retrieveDataViaGetters($object, array $properties)
    {
        $result = array();
        foreach ($properties as $propertyName) {
            $getterName = 'get' . ucfirst($propertyName);
            $result[$propertyName] = $object->$getterName();
        }
        return $result;
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
        $this->assertEquals(array('Module_One', 'File'), FileId::extractModule('Module_One::File'));
        $this->assertEquals(array('', 'File'), FileId::extractModule('File'));
        $this->assertEquals(
            array('Module_One', 'File::SomethingElse'),
            FileId::extractModule('Module_One::File::SomethingElse')
        );
    }
}
