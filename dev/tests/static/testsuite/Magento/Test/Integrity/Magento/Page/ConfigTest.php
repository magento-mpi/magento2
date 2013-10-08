<?php
/**
 * Test page_layouts.xsd and xml files
 *
 * Fined "page_layouts.xml" files in code tree and validate them.  Also verify schema fails on an invalid xml and
 * passes on a valid xml
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity\Magento\Page;

class ConfigTest extends \Magento\TestFramework\Integrity\AbstractConfig
{
    public function testFileSchemaUsingInvalidXml()
    {
        $expectedErrors = array("Element 'layout': The attribute 'id' is required but missing.");
        parent::testFileSchemaUsingInvalidXml($expectedErrors);
    }

    public function testSchemaUsingInvalidXml()
    {
        $expectedErrors = array(
            "Element 'layouts': No match found for key-sequence ['bad_ref'] of keyref 'layout-ref'.",
            "Element 'layout': Missing child element(s). Expected is ( label ).",
        );
        parent::testSchemaUsingInvalidXml($expectedErrors);
    }

    public function testSchemaUsingPartialXml()
    {
        $expectedErrors = array(
            "Element 'layout': Missing child element(s). Expected is ( label ).",
            "Element 'layout': Missing child element(s). Expected is ( template ).",
            "Element 'layout': Missing child element(s). Expected is ( layout_handle )."
        );
        parent::testSchemaUsingPartialXml($expectedErrors);
    }

    /**
     * Returns the name of the XSD file to be used to validate the XML
     *
     * @return string
     */
    protected function _getXsd()
    {
        return '/app/code/Magento/Page/etc/page_layouts.xsd';
    }

    /**
     * The location of a single valid complete xml file
     *
     * @return string
     */
    protected function _getKnownValidXml()
    {
        return __DIR__ . '/_files/valid_page_layouts.xml';
    }

    /**
     * The location of a single known invalid complete xml file
     *
     * @return string
     */
    protected function _getKnownInvalidXml()
    {
        return __DIR__ . '/_files/invalid_page_layouts.xml';
    }

    /**
     * The location of a single known valid partial xml file
     *
     * @return string
     */
    protected function _getKnownValidPartialXml()
    {
        return __DIR__ . '/_files/valid_page_layouts_partial.xml';
    }

    /**
     * Returns the name of the XSD file to be used to validate partial XML
     *
     * @return string
     */
    protected function _getFileXsd()
    {
        return '/app/code/Magento/Page/etc/page_layouts_file.xsd';
    }

    /**
     * The location of a single known invalid partial xml file
     *
     * @return string
     */
    protected function _getKnownInvalidPartialXml()
    {
        return __DIR__ . '/_files/invalid_page_layouts_partial.xml';
    }

    /**
     * Returns the name of the xml files to validate
     *
     * @return string
     */
    protected function _getXmlName()
    {
        return 'page_layouts.xml';
    }
}
