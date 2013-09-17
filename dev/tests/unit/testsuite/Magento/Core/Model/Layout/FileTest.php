<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Layout_FileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Layout_File
     */
    private $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_theme;

    protected function setUp()
    {
        $this->_theme = $this->getMockForAbstractClass('Magento_Core_Model_ThemeInterface');
        $this->_model = new Magento_Core_Model_Layout_File(__FILE__, 'Fixture_TestModule', $this->_theme);
    }

    public function testGetFilename()
    {
        $this->assertEquals(__FILE__, $this->_model->getFilename());
    }

    public function testGetName()
    {
        $this->assertEquals('FileTest.php', $this->_model->getName());
    }

    public function testGetModule()
    {
        $this->assertEquals('Fixture_TestModule', $this->_model->getModule());
    }

    public function testGetTheme()
    {
        $this->assertSame($this->_theme, $this->_model->getTheme());
    }
}
