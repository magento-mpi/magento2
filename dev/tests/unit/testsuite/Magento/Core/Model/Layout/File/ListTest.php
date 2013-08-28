<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Layout_File_ListTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Layout_File_List
     */
    private $_model;

    /**
     * @var Magento_Core_Model_Layout_File
     */
    private $_baseFile;

    /**
     * @var Magento_Core_Model_Layout_File
     */
    private $_themeFile;

    protected function setUp()
    {
        $this->_baseFile = $this->_createLayoutFile('fixture.xml', 'Fixture_TestModule');
        $this->_themeFile = $this->_createLayoutFile('fixture.xml', 'Fixture_TestModule', 'area/theme/path');
        $this->_model = new Magento_Core_Model_Layout_File_List();
        $this->_model->add(array($this->_baseFile, $this->_themeFile));
    }

    /**
     * Return newly created theme layout file with a mocked theme
     *
     * @param string $filename
     * @param string $module
     * @param string|null $themeFullPath
     * @return PHPUnit_Framework_MockObject_MockObject|Magento_Core_Model_ThemeInterface
     */
    protected function _createLayoutFile($filename, $module, $themeFullPath = null)
    {
        $theme = null;
        if ($themeFullPath !== null) {
            $theme = $this->getMockForAbstractClass('Magento_Core_Model_ThemeInterface');
            $theme->expects($this->any())->method('getFullPath')->will($this->returnValue($themeFullPath));
        }
        return new Magento_Core_Model_Layout_File($filename, $module, $theme);
    }

    public function testGetAll()
    {
        $this->assertSame(array($this->_baseFile, $this->_themeFile), $this->_model->getAll());
    }

    public function testAddBaseFile()
    {
        $file = $this->_createLayoutFile('new.xml', 'Fixture_TestModule');
        $this->_model->add(array($file));
        $this->assertSame(array($this->_baseFile, $this->_themeFile, $file), $this->_model->getAll());
    }

    public function testAddThemeFile()
    {
        $file = $this->_createLayoutFile('new.xml', 'Fixture_TestModule', 'area/theme/path');
        $this->_model->add(array($file));
        $this->assertSame(array($this->_baseFile, $this->_themeFile, $file), $this->_model->getAll());
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Layout file 'test/fixture.xml' is indistinguishable from the file 'fixture.xml'
     */
    public function testAddBaseFileException()
    {
        $file = $this->_createLayoutFile('test/fixture.xml', 'Fixture_TestModule');
        $this->_model->add(array($file));
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Layout file 'test/fixture.xml' is indistinguishable from the file 'fixture.xml'
     */
    public function testAddThemeFileException()
    {
        $file = $this->_createLayoutFile('test/fixture.xml', 'Fixture_TestModule', 'area/theme/path');
        $this->_model->add(array($file));
    }

    public function testReplaceBaseFile()
    {
        $file = $this->_createLayoutFile('test/fixture.xml', 'Fixture_TestModule');
        $this->_model->replace(array($file));
        $this->assertSame(array($file, $this->_themeFile), $this->_model->getAll());
    }

    public function testReplaceThemeFile()
    {
        $file = $this->_createLayoutFile('test/fixture.xml', 'Fixture_TestModule', 'area/theme/path');
        $this->_model->replace(array($file));
        $this->assertSame(array($this->_baseFile, $file), $this->_model->getAll());
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Overriding layout file 'new.xml' does not match to any of the files
     */
    public function testReplaceBaseFileException()
    {
        $file = $this->_createLayoutFile('new.xml', 'Fixture_TestModule');
        $this->_model->replace(array($file));
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Overriding layout file 'test/fixture.xml' does not match to any of the files
     */
    public function testReplaceBaseFileEmptyThemePathException()
    {
        $file = $this->_createLayoutFile('test/fixture.xml', 'Fixture_TestModule', '');
        $this->_model->replace(array($file));
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Overriding layout file 'new.xml' does not match to any of the files
     */
    public function testReplaceThemeFileException()
    {
        $file = $this->_createLayoutFile('new.xml', 'Fixture_TestModule', 'area/theme/path');
        $this->_model->replace(array($file));
    }
}
