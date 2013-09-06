<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Widget_Model_WidgetTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Widget_Model_Config_Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storage;

    /**
     * @var Magento_Widget_Model_Widget
     */
    protected $_model;

    public function setUp()
    {
        $this->_storage = $this->getMockBuilder('Magento_Widget_Model_Config_Data')
            ->disableOriginalConstructor()
            ->getMock();
        $viewUrl = $this->getMockBuilder('Magento_Core_Model_View_Url')
            ->disableOriginalConstructor()
            ->getMock();
        $viewFileSystem = $this->getMockBuilder('Magento_Core_Model_View_FileSystem')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_model = new Magento_Widget_Model_Widget($this->_storage, $viewUrl, $viewFileSystem);
    }

    public function testGetWidgets()
    {
        $expected = array('val1', 'val2');
        $this->_storage->expects($this->once())->method('get')
            ->will($this->returnValue($expected));
        $result = $this->_model->getWidgets();
        $this->assertEquals($expected, $result);
    }

    public function testGetWidgetsWithFilter()
    {
        $configFile = __DIR__ . '/_files/mappedConfigArrayAll.php';
        $widgets = include $configFile;
        $this->_storage->expects($this->once())->method('get')
            ->will($this->returnValue($widgets));
        $result = $this->_model->getWidgets(array(
            'name' => 'CMS Page Link',
            'description' => 'Link to a CMS Page',));
        $configFileOne = __DIR__ . '/_files/mappedConfigArray1.php';
        $expected = array('cms_page_link' => include $configFileOne);
        $this->assertEquals($expected, $result);
    }

    public function testGetWidgetsWithUnknownFilter()
    {
        $configFile = __DIR__ . '/_files/mappedConfigArrayAll.php';
        $widgets = include $configFile;
        $this->_storage->expects($this->once())->method('get')
            ->will($this->returnValue($widgets));
        $result = $this->_model->getWidgets(array(
            'name' => 'unknown',
            'description' => 'unknown',));
        $expected = array();
        $this->assertEquals($expected, $result);
    }

    public function testGetWidgetByClassType()
    {
        $widgetOne = array(
            '@' => array(
                'type' => 'type1',
            )
        );
        $widgets = array(
            'widget1' => $widgetOne
        );
        $this->_storage->expects($this->any())->method('get')
            ->will($this->returnValue($widgets));
        $this->assertEquals($widgetOne, $this->_model->getWidgetByClassType('type1'));
        $this->assertNull($this->_model->getWidgetByClassType('type2'));
    }
}
