<?php
/**
 * Find "payment.xml" files and validate them
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity\Magento\Payment\Model;

class ConfigTest extends \Magento\TestFramework\Integrity\AbstractConfig
{
    public function testSchemaUsingInvalidXml($expectedErrors = null)
    {
        // @codingStandardsIgnoreStart
        $expectedErrors = array(
            "Element 'route', attribute 'method': [facet 'enumeration'] The value 'PATCH' is not an element of the set {'GET', 'PUT', 'POST', 'DELETE'}.",
            "Element 'route', attribute 'method': 'PATCH' is not a valid value of the local atomic type.",
            "Element 'resource', attribute 'ref': [facet 'pattern'] The value 'a resource' is not accepted by the pattern '.+::.+(, ?.+::.+)*'.",
            "Element 'resource', attribute 'ref': 'a resource' is not a valid value of the local atomic type.",
            "Element 'data': Missing child element(s). Expected is ( parameter ).",
            "Element 'route': Missing child element(s). Expected is ( service ).",
        );
        // @codingStandardsIgnoreEnd
        parent::testSchemaUsingInvalidXml($expectedErrors);
    }

    /**
     * Returns the name of the xml files to validate
     *
     * @return string
     */
    protected function _getXmlName()
    {
        return 'webapi.xml';
    }

    /**
     * The location of a single valid complete xml file
     *
     * @return string
     */
    protected function _getKnownValidXml()
    {
        return __DIR__ . '/_files/webapi.xml';
    }

    /**
     * The location of a single known invalid complete xml file
     *
     * @return string
     */
    protected function _getKnownInvalidXml()
    {
        return __DIR__ . '/_files/invalid_webapi.xml';
    }

    /**
     * The location of a single known valid partial xml file
     *
     * @return string
     */
    protected function _getKnownValidPartialXml()
    {
        return null;
    }

    /**
     * The location of a single known invalid partial xml file
     *
     * @return string
     */
    protected function _getKnownInvalidPartialXml()
    {
        return null;
    }

    /**
     * Returns the name of the XSD file to be used to validate the XSD
     *
     * @return string
     */
    protected function _getXsd()
    {
        return '/app/code/Magento/Webapi/etc/webapi.xsd';
    }

    /**
     * Returns the name of the XSD file to be used to validate partial XML
     *
     * @return string
     */
    protected function _getFileXsd()
    {
        return null;
    }
}
