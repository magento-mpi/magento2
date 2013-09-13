<?php
/**
 * Find "install_wizard.xml" file and validate
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Integrity_Magento_Install_ConfigTest extends Integrity_ConfigAbstract
{
    public function testFileSchemaUsingPartialXml()
    {
        $this->markTestSkipped('countries.xml does not have a partial schema');
    }

    public function testFileSchemaUsingInvalidXml()
    {
        $this->markTestSkipped('countries.xml does not have a partial schema');
    }

    public function testSchemaUsingPartialXml()
    {
        $this->markTestSkipped('countries.xml does not have a partial schema');
    }

    protected function _getXsd()
    {
        return "/app/code/Magento/Install/etc/install_wizard.xsd";
    }

    protected function _getKnownValidXml()
    {
        return __DIR__ . '/_files/install_wizard.xml';
    }

    protected function _getKnownInvalidXml()
    {
        return __DIR__ . '/_files/invalid_install_wizard.xml';
    }

    protected function _getKnownValidPartialXml()
    {
        return '';
    }

    protected function _getFileXsd()
    {
        return '';
    }

    protected function _getKnownInvalidPartialXml()
    {
        return '';
    }

    protected function _getXmlName()
    {
        return 'install_wizard.xml';
    }
}
