<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Configuration for reports
 */
class Config extends \Magento\Object
{
    public function getGlobalConfig()
    {
        $dom = new DOMDocument();
        $dom->load(\Mage::getModuleDir('etc', 'Magento_Reports') . DS . 'flexConfig.xml');

        $baseUrl = $dom->createElement('baseUrl');
        $baseUrl->nodeValue = \Mage::getBaseUrl();

        $dom->documentElement->appendChild($baseUrl);

        return $dom->saveXML();
    }

    public function getLanguage()
    {
        return file_get_contents(\Mage::getModuleDir('etc', 'Magento_Reports') . DS . 'flexLanguage.xml');
    }

    public function getDashboard()
    {
        return file_get_contents(\Mage::getModuleDir('etc', 'Magento_Reports') . DS . 'flexDashboard.xml');
    }
}
