<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Layout;

class FileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Layout\File
     */
    private $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_theme;

    protected function setUp()
    {
        $this->_theme = $this->getMockForAbstractClass('Magento\Core\Model\ThemeInterface');
        $this->_model = new \Magento\Core\Model\Layout\File(__FILE__, 'Fixture_TestModule', $this->_theme);
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
