<?php
/**
 * Find "payment.xml" files and validate them
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Test\Integrity\Magento\Payment\Model;

class ConfigTest extends \Magento\TestFramework\Integrity\AbstractConfig
{
    public function testSchemaUsingInvalidXml($expectedErrors = null)
    {
        $expectedErrors = [
            "Element 'type': The attribute 'id' is required but missing.",
            "Element 'type': Missing child element(s). Expected is ( label ).",
            "Element 'group': The attribute 'id' is required but missing.",
            "Element 'group': Missing child element(s). Expected is ( label ).",
        ];
        parent::testSchemaUsingInvalidXml($expectedErrors);
    }

    public function testFileSchemaUsingInvalidXml($expectedErrors = null)
    {
        $expectedErrors = [
            "Element 'type': The attribute 'id' is required but missing.",
            "Element 'type': The attribute 'id' is required but missing.",
            "Element 'group': The attribute 'id' is required but missing.",
        ];
        parent::testFileSchemaUsingInvalidXml($expectedErrors);
    }

    public function testSchemaUsingPartialXml($expectedErrors = null)
    {
        $expectedErrors = [
            "Element 'type': The attribute 'order' is required but missing.",
            "Element 'type': Missing child element(s). Expected is ( label ).",
        ];
        parent::testSchemaUsingPartialXml($expectedErrors);
    }

    /**
     * Returns the name of the xml files to validate
     *
     * @return string
     */
    protected function _getXmlName()
    {
        return 'payment.xml';
    }

    /**
     * The location of a single valid complete xml file
     *
     * @return string
     */
    protected function _getKnownValidXml()
    {
        return __DIR__ . '/_files/payment.xml';
    }

    /**
     * The location of a single known invalid complete xml file
     *
     * @return string
     */
    protected function _getKnownInvalidXml()
    {
        return __DIR__ . '/_files/invalid_payment.xml';
    }

    /**
     * The location of a single known valid partial xml file
     *
     * @return string
     */
    protected function _getKnownValidPartialXml()
    {
        return __DIR__ . '/_files/payment_partial.xml';
    }

    /**
     * The location of a single known invalid partial xml file
     *
     * @return string
     */
    protected function _getKnownInvalidPartialXml()
    {
        return __DIR__ . '/_files/invalid_payment_partial.xml';
    }

    /**
     * Returns the name of the XSD file to be used to validate the XSD
     *
     * @return string
     */
    protected function _getXsd()
    {
        return '/app/code/Magento/Payment/etc/payment.xsd';
    }

    /**
     * Returns the name of the XSD file to be used to validate partial XML
     *
     * @return string
     */
    protected function _getFileXsd()
    {
        return '/app/code/Magento/Payment/etc/payment_file.xsd';
    }
}
