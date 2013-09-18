<?php
/**
 * Test persistent.xsd and xml files.
 *
 * Find "persistent.xml" files in code tree and validate them.  Also verify schema fails on an invalid xml and
 * passes on a valid xml.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Integrity_Magento_Persistent_ConfigTest extends Integrity_ConfigAbstract
{

    /**
     * Returns the name of the XSD file to be used to validate the XML
     *
     * @return string
     */
    protected function _getXsd()
    {
        return '/app/code/Magento/Persistent/etc/persistent.xsd';
    }

    /**
     * The location of a single valid complete xml file
     *
     * @return string
     */
    protected function _getKnownValidXml()
    {
        return __DIR__ . '/_files/valid_persistent.xml';
    }

    /**
     * The location of a single known invalid complete xml file
     *
     * @return string
     */
    protected function _getKnownInvalidXml()
    {
        return __DIR__ . '/_files/invalid_persistent.xml';

    }

    /**
     * The location of a single known valid partial xml file
     *
     * @return string
     */
    protected function _getKnownValidPartialXml()
    {
        return '';
    }

    /**
     * Returns the name of the XSD file to be used to validate partial XML
     *
     * @return string
     */
    protected function _getFileXsd()
    {
        return '';
    }

    /**
     * The location of a single known invalid partial xml file
     *
     * @return string
     */
    protected function _getKnownInvalidPartialXml()
    {
        return '';
    }

    /**
     * Returns the name of the xml files to validate
     *
     * @return string
     */
    protected function _getXmlName()
    {
        return 'persistent.xml';
    }

    public function testFileSchemaUsingInvalidXml()
    {
        $this->markTestSkipped('persistent.xml does not have a partial schema');
    }

    public function testSchemaUsingPartialXml()
    {
        $this->markTestSkipped('persistent.xml does not have a partial schema');
    }

    public function testFileSchemaUsingPartialXml()
    {
        $this->markTestSkipped('persistent.xml does not have a partial schema');
    }
}
