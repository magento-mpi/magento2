<?php
/**
 * Magento_Widget_Model_Widget_Instance
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Widget_Model_Widget_InstanceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Widget_Model_Config_Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_widgetModelMock;

    /**
     * @var Magento_Core_Model_View_FileSystem|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_viewFileSystemMock;

    /** @var  Magento_Core_Model_Config|PHPUnit_Framework_MockObject_MockObject */
    protected $_coreConfigMock;

    /**
     * @var Magento_Widget_Model_Widget_Instance
     */
    protected $_model;

    /** @var  Magento_Widget_Model_Config_Reader */
    protected $_readerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheTypesListMock;

    public function setUp()
    {
        $this->_widgetModelMock = $this->getMockBuilder('Magento_Widget_Model_Widget')
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock = $this->getMockBuilder('Magento_Core_Model_Context')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_viewFileSystemMock = $this->getMockBuilder('Magento_Core_Model_View_FileSystem')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_coreConfigMock = $this->getMockBuilder('Magento_Core_Model_Config')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_cacheTypesListMock = $this->getMock('Magento_Core_Model_Cache_TypeListInterface');
        $this->_readerMock = $this->getMockBuilder('Magento_Widget_Model_Config_Reader')
            ->disableOriginalConstructor()
            ->getMock();
        $registryMock = $this->getMockBuilder('Magento_Core_Model_Registry')
            ->disableOriginalConstructor()
            ->getMock();
        $coreData = $this->getMockBuilder('Magento_Core_Helper_Data')
            ->disableOriginalConstructor()
            ->getMock();
        $widgetData = $this->getMockBuilder('Magento_Widget_Helper_Data')
            ->disableOriginalConstructor()
            ->getMock();
        $productType = $this->getMockBuilder('Magento_Catalog_Model_Product_Type')
            ->disableOriginalConstructor()
            ->getMock();
        /** @var Magento_Widget_Model_Widget_Instance _model */
        $this->_model = $this->getMock(
            'Magento_Widget_Model_Widget_Instance',
            array('_construct'),
            array(
                $widgetData, $coreData, $contextMock, $registryMock, $this->_viewFileSystemMock,
                $this->_cacheTypesListMock, $productType, $this->_readerMock, $this->_widgetModelMock,
                $this->_coreConfigMock
            ),
            '',
            true
        );
    }

    public function testGetWidgetConfigAsArray()
    {
        $widget = array(
            '@' => array(
                'type' => 'Magento_Cms_Block_Widget_Page_Link',
                'module' => 'Magento_Cms',
            ),
            'name' => 'CMS Page Link',
            'description' => 'Link to a CMS Page',
            'is_email_compatible' => 'true',
            'placeholder_image' => 'Magento_Cms::images/widget_page_link.gif',
            'parameters' => array(
                'page_id' => array(
                    '@' => array(
                        'type' => 'complex',
                    ),
                    'type' => 'label',
                    'helper_block' => array(
                        'type' => 'Magento_Adminhtml_Block_Cms_Page_Widget_Chooser',
                        'data' => array(
                            'button' => array(
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
        $xmlFile = __DIR__ . '/../_files/widget.xml';
        $this->_viewFileSystemMock->expects($this->once())->method('getFilename')
            ->will($this->returnValue($xmlFile));
        $themeConfigFile = __DIR__ . '/../_files/mappedConfigArrayAll.php';
        $themeConfig = include $themeConfigFile;
        $this->_readerMock->expects($this->once())->method('readFile')->with($this->equalTo($xmlFile))
            ->will($this->returnValue($themeConfig));

        $result = $this->_model->getWidgetConfigAsArray();

        $expectedConfigFile = __DIR__ . '/../_files/mappedConfigArray1.php';
        $expectedConfig = include $expectedConfigFile;
        $this->assertEquals($expectedConfig, $result);
    }

    public function testGetWidgetTemplates()
    {
        $expectedConfigFile = __DIR__ . '/../_files/mappedConfigArray1.php';
        $widget = include $expectedConfigFile;
        $this->_widgetModelMock->expects($this->once())->method('getWidgetByClassType')
            ->will($this->returnValue($widget));
        $this->_viewFileSystemMock->expects($this->once())->method('getFilename')
            ->will($this->returnValue(''));
        $expectedTemplates = array(
            'default' => array(
                'value' => 'product/widget/link/link_block.phtml',
                'label' => 'Product Link Block Template',
            ),
            'link_inline' => array(
                'value' => 'product/widget/link/link_inline.phtml',
                'label' => 'Product Link Inline Template',
            )
        );
        $this->assertEquals($expectedTemplates, $this->_model->getWidgetTemplates());
    }

    public function testGetWidgetTemplatesValueOnly()
    {
        $widget = array(
            '@' => array(
                'type' => 'Magento_Cms_Block_Widget_Page_Link',
                'module' => 'Magento_Cms',
            ),
            'name' => 'CMS Page Link',
            'description' => 'Link to a CMS Page',
            'is_email_compatible' => 'true',
            'placeholder_image' => 'Magento_Cms::images/widget_page_link.gif',
            'parameters' => array(
                'template' => array(
                    'values' => array(
                        'default' => array(
                            'value' => 'product/widget/link/link_block.phtml',
                            'label' => 'Template'
                        )
                    ),
                    'type' => 'select',
                    'visible' => 'true',
                    'label' => 'Template',
                    'value' => 'product/widget/link/link_block.phtml',
                ),
            ),
        );
        $this->_widgetModelMock->expects($this->once())->method('getWidgetByClassType')
            ->will($this->returnValue($widget));
        $this->_viewFileSystemMock->expects($this->once())->method('getFilename')
            ->will($this->returnValue(''));
        $expectedTemplates = array(
            'default' => array(
                'value' => 'product/widget/link/link_block.phtml',
                'label' => 'Template',
            ),
        );
        $this->assertEquals($expectedTemplates, $this->_model->getWidgetTemplates());
    }

    public function testGetWidgetTemplatesNoTemplate()
    {
        $widget = array(
            '@' => array(
                'type' => 'Magento_Cms_Block_Widget_Page_Link',
                'module' => 'Magento_Cms',
            ),
            'name' => 'CMS Page Link',
            'description' => 'Link to a CMS Page',
            'is_email_compatible' => 'true',
            'placeholder_image' => 'Magento_Cms::images/widget_page_link.gif',
            'parameters' => array(
            ),
        );
        $this->_widgetModelMock->expects($this->once())->method('getWidgetByClassType')
            ->will($this->returnValue($widget));
        $this->_viewFileSystemMock->expects($this->once())->method('getFilename')
            ->will($this->returnValue(''));
        $expectedTemplates = array();
        $this->assertEquals($expectedTemplates, $this->_model->getWidgetTemplates());
    }

    public function testGetWidgetSupportedContainers()
    {
        $expectedConfigFile = __DIR__ . '/../_files/mappedConfigArray1.php';
        $widget = include $expectedConfigFile;
        $this->_widgetModelMock->expects($this->once())->method('getWidgetByClassType')
            ->will($this->returnValue($widget));
        $this->_viewFileSystemMock->expects($this->once())->method('getFilename')
            ->will($this->returnValue(''));
        $expectedContainers = array('left', 'content');
        $this->assertEquals($expectedContainers, $this->_model->getWidgetSupportedContainers());
    }

    public function testGetWidgetSupportedContainersNoContainer()
    {
        $widget = array(
            '@' => array(
                'type' => 'Magento_Cms_Block_Widget_Page_Link',
                'module' => 'Magento_Cms',
            ),
            'name' => 'CMS Page Link',
            'description' => 'Link to a CMS Page',
            'is_email_compatible' => 'true',
            'placeholder_image' => 'Magento_Cms::images/widget_page_link.gif',
        );
        $this->_widgetModelMock->expects($this->once())->method('getWidgetByClassType')
            ->will($this->returnValue($widget));
        $this->_viewFileSystemMock->expects($this->once())->method('getFilename')
            ->will($this->returnValue(''));
        $expectedContainers = array();
        $this->assertEquals($expectedContainers, $this->_model->getWidgetSupportedContainers());
    }

    public function testGetWidgetSupportedTemplatesByContainers()
    {
        $expectedConfigFile = __DIR__ . '/../_files/mappedConfigArray1.php';
        $widget = include $expectedConfigFile;
        $this->_widgetModelMock->expects($this->once())->method('getWidgetByClassType')
            ->will($this->returnValue($widget));
        $this->_viewFileSystemMock->expects($this->once())->method('getFilename')
            ->will($this->returnValue(''));
        $expectedTemplates = array(
            array(
                'value' => 'product/widget/link/link_block.phtml',
                'label' => 'Product Link Block Template',
            ),
            array(
                'value' => 'product/widget/link/link_inline.phtml',
                'label' => 'Product Link Inline Template',
            )
        );
        $this->assertEquals($expectedTemplates, $this->_model->getWidgetSupportedTemplatesByContainer('left'));
    }

    public function testGetWidgetSupportedTemplatesByContainers2()
    {
        $expectedConfigFile = __DIR__ . '/../_files/mappedConfigArray1.php';
        $widget = include $expectedConfigFile;
        $this->_widgetModelMock->expects($this->once())->method('getWidgetByClassType')
            ->will($this->returnValue($widget));
        $this->_viewFileSystemMock->expects($this->once())->method('getFilename')
            ->will($this->returnValue(''));
        $expectedTemplates = array(
            array(
                'value' => 'product/widget/link/link_block.phtml',
                'label' => 'Product Link Block Template',
            ),
        );
        $this->assertEquals($expectedTemplates, $this->_model->getWidgetSupportedTemplatesByContainer('content'));
    }

    public function testGetWidgetSupportedTemplatesByContainersNoSupportedContainersSpecified()
    {
        $widget = array(
            '@' => array(
                'type' => 'Magento_Cms_Block_Widget_Page_Link',
                'module' => 'Magento_Cms',
            ),
            'name' => 'CMS Page Link',
            'description' => 'Link to a CMS Page',
            'is_email_compatible' => 'true',
            'placeholder_image' => 'Magento_Cms::images/widget_page_link.gif',
            'parameters' => array(
                'template' => array(
                    'values' => array(
                        'default' => array(
                            'value' => 'product/widget/link/link_block.phtml',
                            'label' => 'Template'
                        )
                    ),
                    'type' => 'select',
                    'visible' => 'true',
                    'label' => 'Template',
                    'value' => 'product/widget/link/link_block.phtml',
                ),
            ),
        );;
        $this->_widgetModelMock->expects($this->once())->method('getWidgetByClassType')
            ->will($this->returnValue($widget));
        $this->_viewFileSystemMock->expects($this->once())->method('getFilename')
            ->will($this->returnValue(''));
        $expectedContainers = array(
            'default' => array(
                'value' => 'product/widget/link/link_block.phtml',
                'label' => 'Template',
            ),
        );
        $this->assertEquals($expectedContainers, $this->_model->getWidgetSupportedTemplatesByContainer('content'));
    }

    public function testGetWidgetSupportedTemplatesByContainersUnknownContainer()
    {
        $expectedConfigFile = __DIR__ . '/../_files/mappedConfigArray1.php';
        $widget = include $expectedConfigFile;
        $this->_widgetModelMock->expects($this->once())->method('getWidgetByClassType')
            ->will($this->returnValue($widget));
        $this->_viewFileSystemMock->expects($this->once())->method('getFilename')
            ->will($this->returnValue(''));
        $expectedTemplates = array();
        $this->assertEquals($expectedTemplates, $this->_model->getWidgetSupportedTemplatesByContainer('unknown'));
    }
}
