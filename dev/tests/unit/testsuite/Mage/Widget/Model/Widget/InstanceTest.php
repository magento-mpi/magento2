<?php
/**
 * Mage_Widget_Model_Widget_Instance
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Widget_Model_Widget_InstanceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Widget_Model_Config_Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_widgetModelMock;

    /**
     * @var Mage_Core_Model_View_FileSystem|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_viewFileSystemMock;

    /** @var  Mage_Core_Model_Config|PHPUnit_Framework_MockObject_MockObject */
    protected $_coreConfigMock;
    /**
     * @var Mage_Widget_Model_Widget_Instance
     */
    protected $_model;

    /** @var  Mage_Widget_Model_Config_Reader */
    protected $_readerMock;

    public function setUp()
    {
        $this->_widgetModelMock = $this->getMockBuilder('Mage_Widget_Model_Widget')
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock = $this->getMockBuilder('Mage_Core_Model_Context')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_viewFileSystemMock = $this->getMockBuilder('Mage_Core_Model_View_FileSystem')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_coreConfigMock = $this->getMockBuilder('Mage_Core_Model_Config')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_readerMock = $this->getMockBuilder('Mage_Widget_Model_Config_Reader')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_model = $this->getMock(
            'Mage_Widget_Model_Widget_Instance',
            array('_construct'),
            array($contextMock, $this->_viewFileSystemMock, $this->_readerMock , $this->_widgetModelMock, $this->_coreConfigMock),
            '',
            true
        );
    }

    public function testGetWidgetConfig()
    {
        $widget = array(
            '@' => array(
                'type' => 'Mage_Cms_Block_Widget_Page_Link',
                'module' => 'Mage_Cms',
                'translate' => 'name description',
            ),
            'name' => 'CMS Page Link',
            'description' => 'Link to a CMS Page',
            'is_email_compatible' => 'true',
            'placeholder_image' => 'Mage_Cms::images/widget_page_link.gif',
            'parameters' => array(
                'page_id' => array(
                    '@' => array(
                        'type' => 'complex',
                        'translate' => 'label',
                    ),
                    'type' => 'label',
                    'helper_block' => array(
                        'type' => 'Mage_Adminhtml_Block_Cms_Page_Widget_Chooser',
                        'data' => array(
                            'button' => array(
                                '@' => array(
                                    'translate' => 'open',
                                ),
                                'open' => 'Select Page...',
                            ),
                        ),
                    ),
                    'visible' => 'true',
                    'required' => 'true',
                    'sort_order' => '10',
                    'label' => 'CMS Page',
                ),
            ),
        );
        $this->_widgetModelMock->expects($this->once())->method('getWidgetByClassType')
            ->will($this->returnValue($widget));
        $xmlFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files'
            . DIRECTORY_SEPARATOR . 'widget.xml';
        $this->_viewFileSystemMock->expects($this->once())->method('getFilename')
            ->will($this->returnValue($xmlFile));
        $themeConfigFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files'
            . DIRECTORY_SEPARATOR . 'mappedConfigArrayAll.php';
        $themeConfig = include $themeConfigFile;
        $expectedConfigFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files'
            . DIRECTORY_SEPARATOR . 'mappedConfigArray1.php';
        $expectedConfig = include $expectedConfigFile;
        $this->_readerMock->expects($this->once())->method('readFile')->with($this->equalTo($xmlFile))
            ->will($this->returnValue($themeConfig));
        $result = $this->_model->getWidgetConfig();
        $this->assertEquals($expectedConfig, $result);
    }
}
