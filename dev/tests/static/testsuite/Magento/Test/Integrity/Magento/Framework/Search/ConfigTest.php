<?php
/**
 * Test search_request.xsd and xml files.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity\Magento\Framework\Search;

class ConfigTest extends \Magento\TestFramework\Integrity\AbstractConfig
{
    /**
     * Returns the name of the XSD file to be used to validate the XML
     *
     * @return string
     */
    protected function _getXsd()
    {
        return '/lib/internal/Magento/Framework/Search/etc/search_request_merged.xsd';
    }

    /**
     * Returns the name of the XSD file to be used to validate partial XML
     *
     * @return string
     */
    protected function _getFileXsd()
    {
        return '/lib/internal/Magento/Framework/Search/etc/search_request.xsd';
    }

    /**
     * The location of a single valid complete xml file
     *
     * @return string
     */
    protected function _getKnownValidXml()
    {
        return __DIR__ . '/_files/valid.xml';
    }

    /**
     * The location of a single known invalid complete xml file
     *
     * @return string
     */
    protected function _getKnownInvalidXml()
    {
        return __DIR__ . '/_files/invalid.xml';
    }

    /**
     * The location of a single known valid partial xml file
     *
     * @return string
     */
    protected function _getKnownValidPartialXml()
    {
        return __DIR__ . '/_files/valid_partial.xml';
    }

    /**
     * @param null $expectedErrors
     */
    public function testSchemaUsingInvalidXml($expectedErrors = null)
    {
        $expectedErrors = array_filter(
            explode(
                "\n",
                "
Element 'from': This element is not expected. Expected is ( filters ).
No match found for key-sequence ['sugegsted_search_container'] of keyref 'requestQueryReference'.
Element 'queryReference': No match found for key-sequence ['fulltext_search_query4'] of keyref 'queryReference'.
"
            )
        );
        parent::testSchemaUsingInvalidXml($expectedErrors);
    }

    /**
     * @param null $expectedErrors
     */
    public function testFileSchemaUsingInvalidXml($expectedErrors = null)
    {
        $expectedErrors = array_filter(
            explode(
                "\n",
                "
Element 'queryReference': The attribute 'ref' is required but missing.
Element 'filterReference': The attribute 'ref' is required but missing.
Element 'filter': The attribute 'field' is required but missing.
Element 'filter': The attribute 'value' is required but missing.
Element 'filterReference': The attribute 'clause' is required but missing.
Element 'filterReference': The attribute 'ref' is required but missing.
Element 'bucket': Missing child element(s). Expected is ( metrics ).
Element 'metric', attribute 'type': [facet 'enumeration'] The value 'sumasdasd' is not an element of the set {'sum', 'count', 'min', 'max'}.
Element 'metric', attribute 'type': 'sumasdasd' is not a valid value of the local atomic type.
Element 'bucket': Missing child element(s). Expected is ( ranges ).
Element 'request': Missing child element(s). Expected is ( from )."
            )
        );
        parent::testFileSchemaUsingInvalidXml($expectedErrors);
    }

    /**
     * Returns the name of the xml files to validate
     *
     * @return string
     */
    protected function _getXmlName()
    {
        return 'search_request.xml';
    }

    /**
     * The location of a single known invalid partial xml file
     *
     * @return string
     */
    protected function _getKnownInvalidPartialXml()
    {
        return __DIR__ . '/_files/invalid_partial.xml';
    }

    public function testSchemaUsingValidXml()
    {
        parent::testSchemaUsingValidXml();
    }
}
