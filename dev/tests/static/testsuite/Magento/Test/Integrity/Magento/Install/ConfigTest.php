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
        return __DIR__ . '/_files/partial_install_wizard.xml';
    }

    protected function _getFileXsd()
    {
        return "/app/code/Magento/Install/etc/install_wizard_file.xsd";
    }

    protected function _getKnownInvalidPartialXml()
    {
        return __DIR__ . '/_files/invalid_partial_install_wizard.xml';
    }

    protected function _getXmlName()
    {
        return 'install_wizard.xml';
    }
}
