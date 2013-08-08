<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Configuration for reports
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */

 class Mage_Reports_Model_Config extends Magento_Object
 {
    public function getGlobalConfig( )
    {
        $dom = new DOMDocument();
        $dom -> load( Mage::getModuleDir('etc','Mage_Reports').DS.'flexConfig.xml' );

        $baseUrl = $dom -> createElement('baseUrl');
        $baseUrl -> nodeValue = Mage::getBaseUrl();

        $dom -> documentElement -> appendChild( $baseUrl );

        return $dom -> saveXML();
    }

    public function getLanguage( )
    {
        return file_get_contents( Mage::getModuleDir('etc','Mage_Reports').DS.'flexLanguage.xml' );
    }

    public function getDashboard( )
    {
        return file_get_contents( Mage::getModuleDir('etc','Mage_Reports').DS.'flexDashboard.xml' );
    }
 }

