<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_UnitPrice
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_PrintedTemplate_Model_Wysiwyg_TemplateParserTest extends PHPUnit_Framework_TestCase
{
    protected $_wysiwygConfig;
    protected $_templateParser;
    protected $_template;

    public function setup()
    {
        $this->_wysiwygConfig = $this->getMockBuilder('Saas_PrintedTemplate_Model_Wysiwyg_Config')
            ->setMethods(array('getFooterSeparator', 'getHeaderSeparator'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_templateParser = $this->getMockBuilder('Saas_PrintedTemplate_Model_Wysiwyg_TemplateParser')
            ->setMethods(array('_getWysiwygConfig'))
            ->getMock();

        $this->_template = $this->getMockBuilder('Saas_PrintedTemplate_Model_Template')
            ->setMethods(array('validate'))
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Test Saas_PrintedTemplate_Model_Wysiwyg_TemplateParser:importContent
     *
     * @param string $footerSeparator
     * @param string $headerSeparator
     * @param string $footer
     * @param string $header
     * @param string $parsedContent
     * @param string $content
     *
     * @dataProvider importContentFooterHeaderProvider()
     */
    public function testShouldParseHeaderFooterContentOnImport(
        $footerSeparator, $headerSeparator, $footer, $header, $parsedContent, $content)
    {
        $this->_wysiwygConfig->expects($this->any())
            ->method('getFooterSeparator')
            ->will($this->returnValue($footerSeparator));

        $this->_wysiwygConfig->expects($this->any())
            ->method('getHeaderSeparator')
            ->will($this->returnValue($headerSeparator));

        $this->_templateParser->expects($this->any())
            ->method('_getWysiwygConfig')
            ->will($this->returnValue($this->_wysiwygConfig));

        $this->_templateParser->importContent($content, $this->_template);

        $this->assertEquals($footer, $this->_template->getFooter(), 'Wrong footer');
        $this->assertEquals($header, $this->_template->getHeader(), 'Wrong header');
        $this->assertEquals($parsedContent, $this->_template->getContent(), 'Wrong content');
    }

    /**
     * Provider for testShouldParseHeaderFooterContentOnImport
     *
     * @return array
     */
    public function importContentFooterHeaderProvider()
    {
        return array(
            array('/', '#', 'footer', 'header', 'content', 'header#content/footer'),
            array('*', '#', false, 'header', 'content/footer', 'header#content/footer'),
            array('/', '*', 'footer', false, 'header#content', 'header#content/footer')
        );
    }

    /**
     * Test Saas_PrintedTemplate_Model_Wysiwyg_TemplateParser:importContent
     */
    public function testShouldReturnProperInstance()
    {
        $this->_wysiwygConfig->expects($this->any())
            ->method('getFooterSeparator')
            ->will($this->returnValue('/'));

        $this->_wysiwygConfig->expects($this->any())
            ->method('getHeaderSeparator')
            ->will($this->returnValue('#'));

        $this->_templateParser->expects($this->any())
            ->method('_getWysiwygConfig')
            ->will($this->returnValue($this->_wysiwygConfig));

        $this->assertInstanceOf(
            'Saas_PrintedTemplate_Model_Wysiwyg_TemplateParser',
            $this->_templateParser->importContent('content', $this->_template),
            'Template Parser returned wrong class instance'
        );
    }

    /**
     * Test Saas_PrintedTemplate_Model_Wysiwyg_TemplateParser:exportContent
     *
     * @param string $footerSeparator
     * @param string $headerSeparator
     * @param string $footer
     * @param string $header
     * @param string $content
     * @param string $fullContent
     *
     * @dataProvider exportContentProvider()
     */
    public function testShouldAssembleFullContentOnExport(
        $footerSeparator, $headerSeparator, $footer, $header, $content, $fullContent)
    {
        $this->_wysiwygConfig->expects($this->any())
            ->method('getFooterSeparator')
            ->will($this->returnValue($footerSeparator));

        $this->_wysiwygConfig->expects($this->any())
            ->method('getHeaderSeparator')
            ->will($this->returnValue($headerSeparator));

        $this->_templateParser->expects($this->any())
            ->method('_getWysiwygConfig')
            ->will($this->returnValue($this->_wysiwygConfig));

        $this->_template->setHeader($header);
        $this->_template->setFooter($footer);
        $this->_template->setContent($content);

        $assembledContent = $this->_templateParser->exportContent($this->_template);

        $this->assertEquals($fullContent, $assembledContent, 'Wrong content');
    }

    /**
     * Provider for testShouldAssembleFullContentOnExport
     *
     * @return array
     */
    public function exportContentProvider()
    {
        return array(
            array('/', '#', 'footer', 'header', 'content', 'header#content/footer'),
            array('*', '#', false, 'header', 'content', 'header#content'),
            array('/', '*', 'footer', false, 'content', 'content/footer')
        );
    }
}