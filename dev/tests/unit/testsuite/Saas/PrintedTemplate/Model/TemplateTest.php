<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_PrintedTemplate_Model_TemplateTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test Saas_PrintedTemplate_Model_Template::validate
     *
     * @param int $pageSize
     * @param bool $hasFooter
     * @param int $footerHeight
     * @param bool $hasHeader
     * @param $headerHeight
     * @param string $message
     *
     * @dataProvider validateDataProvider
     */
    public function testValidate($pageSize, $hasFooter, $footerHeight, $hasHeader, $headerHeight, $message)
    {
        $this->setExpectedException('UnexpectedValueException', $message);
        $mockedMethods = array('getFooterHeight','_getStandardizedLengthValue','getHeaderHeight', 'getPageSize');
        $template = $this->getMockBuilder('Saas_PrintedTemplate_Model_Template')
            ->setMethods($mockedMethods)
            ->disableOriginalConstructor()
            ->getMock();
        $template->setData('page_size', $pageSize)
            ->setHasFooter($hasFooter)
            ->setHasHeader($hasHeader)
            ->setFooter($footerHeight)
            ->setHeader($headerHeight)
            ->setFooterAutoHeight(false)
            ->setHeaderAutoHeight(false);

        $headerLength = new Zend_Measure_Length($headerHeight, Zend_Measure_Length::MILLIMETER);
        $footerLength = new Zend_Measure_Length($footerHeight, Zend_Measure_Length::MILLIMETER);

        if ($pageSize) {
            $config = array(
                'name' => 'a4',
                'height' => new Zend_Measure_Length($pageSize, Zend_Measure_Length::MILLIMETER),
                'width' => new Zend_Measure_Length($pageSize, Zend_Measure_Length::MILLIMETER),
            );
            $pageSizeObj = new Saas_PrintedTemplate_Model_PageSize(array('sizeInfo' => $config));
            $template->expects($this->any())
                ->method('getPageSize')
                ->will($this->returnValue($pageSizeObj));
        }

        $template->expects($this->any())
            ->method('getFooterHeight')
            ->will($this->returnValue($footerLength));

        $template->expects($this->any())
            ->method('getHeaderHeight')
            ->will($this->returnValue($headerLength));

        $template->expects($this->any())
            ->method('_getStandardizedLengthValue')
            ->will($this->returnValue($pageSize));

        $template->validate();
    }

    /**
     * Provider for testValidate
     *
     * @return array
     */
    public function validateDataProvider()
    {
        return array(
            array(
                false, true, 10, true, 10, 'Page size should be defined.'
            ),
            array(
                100, true, -10, true, 10, 'Footer height should be a numeric value which is greater than zero.'
            ),
            array(
                100, true, 10, true, -10, 'Header height should be a numeric value which is greater than zero.'
            ),
            array(
                100, true, 60, true, 60, 'The height of header and footer can not be greater than page height.'
            )
        );
    }

    /**
     * Test Saas_PrintedTemplate_Model_Template::loadDefault
     *
     * @param string|int $templateId    Initial template id
     * @param string $fileContent   Template file content
     * @param string|int $expectedId    Expected Template id
     * @param string $expectedContent   Expected template content after loading
     *
     * @dataProvider loadDefaultDataProvider
     */
    public function testLoadDefault($templateId, $fileContent, $expectedId = null, $expectedContent = '')
    {
        $template = $this->getMockBuilder('Saas_PrintedTemplate_Model_Template')
            ->setMethods(array('getTemplateFile','_getTemplateParser', '_importContent'))
            ->disableOriginalConstructor()
            ->getMock();

        $file = file_get_contents(__DIR__ . '/../_files/config.xml');
        $xml = simplexml_load_string($file, 'Magento_Simplexml_Element');
        $array = $xml->asArray();

        $template->expects($this->any())
            ->method('getTemplateFile')
            ->will($this->returnValue($fileContent));

        $template->expects($this->any())
            ->method('_importContent')
            ->will(
                $this->returnCallback(
                    function($text) use ($template)
                    {
                        $template->setContent($text);
                    }
                )
            );

        $template::setDefaultTemplates($array['global']['template']['printed']);

        $template->loadDefault($templateId);

        $this->assertEquals($expectedId, $template->getId());
        $this->assertEquals($expectedContent, trim($template->getContent()));
    }

    /**
     * Provide data for testLoadDefault method
     *
     * @return array
     */
    public function loadDefaultDataProvider()
    {
        return array(
            array('sales', '', null, ''),
            array(
                'sales_pdf_invoice_printed_template',
                '<!--@name Invoice {{var invoice.increment_id}} @-->Invoice template content',
                'sales_pdf_invoice_printed_template',
                'Invoice template content'
            )
        );
    }

    /**
     * Test Saas_PrintedTemplate_Model_Template::getProcessedContent
     */
    public function testGetProcessedContent()
    {
        $template = $this->getMockBuilder('Saas_PrintedTemplate_Model_Template')
            ->setMethods(array('getTemplateFilter','getDesignConfig'))
            ->disableOriginalConstructor()
            ->getMock();

        $viewUrl = $this->getMock('Mage_Core_Model_View_Url', array(), array(), '', false);
        $template->expects($this->any())
            ->method('getTemplateFilter')
            ->will($this->returnValue(new Mage_Widget_Model_Template_Filter(
                $this->getMockBuilder('Mage_Widget_Model_Widget')->disableOriginalConstructor()->getMock(),
                $this->getMockBuilder('Mage_Widget_Model_Resource_Widget')->disableOriginalConstructor()->getMock(),
                $this->getMockBuilder('Mage_Core_Model_App')->disableOriginalConstructor()->getMock(),
                $viewUrl
            )));

        $template->expects($this->any())
            ->method('getDesignConfig')
            ->will($this->returnValue(new Magento_Object(array(
                'area' => 'frontend',
                'store' => '1'
            ))));

        $template->setContent('variable = {{var key}}');
        $result = $template->getProcessedContent(array('key' => 'value'));

        $this->assertEquals('variable = value', $result);
    }

    /**
     * Test Saas_PrintedTemplate_Model_Template::loadForStore
     */
    public function testLoadForStore()
    {
        $templateId = 1;

        $template = $this->getMockBuilder('Saas_PrintedTemplate_Model_Template')
            ->setMethods(array('load','loadDefault','getDesignConfig'))
            ->disableOriginalConstructor()
            ->getMock();

        $template->expects($this->any())
            ->method('getDesignConfig')
            ->will($this->returnValue(new Magento_Object(array(
                'area' => 'frontend',
                'store' => '1'
            ))));

        $template->expects($this->once())
            ->method('load')
            ->with($this->equalTo($templateId))
            ->will(
                $this->returnCallback(
                    function($id) use ($template)
                    {
                        $template->setId($id);
                    }
                )
            );

        $template->expects($this->never())
            ->method('loadDefault');

        $template->loadForStore($templateId);
        $this->assertEquals($templateId, $template->getId());
    }

    /**
     * Test for method Saas_PrintedTemplate_Model_Template::loadForStore
     * TemplateId is string
     */
    public function testLoadForStoreNonExistentTemplate()
    {
        $templateId = 1;
        $message = 'Cannot load printed template; please ensure that template for the store of the order is selected.';

        $template = $this->getMockBuilder('Saas_PrintedTemplate_Model_Template')
            ->setMethods(array('load','loadDefault','getDesignConfig'))
            ->disableOriginalConstructor()
            ->getMock();

        $template->expects($this->any())
            ->method('getDesignConfig')
            ->will($this->returnValue(new Magento_Object(array(
                'area' => 'frontend',
                'store' => '1'
            ))));

        $template->expects($this->once())
            ->method('load')
            ->with($this->equalTo($templateId))
            ->will($this->returnValue($template));

        $template->expects($this->never())
            ->method('loadDefault');

        $this->setExpectedException('UnexpectedValueException', $message);
        $template->loadForStore($templateId);
    }
}
