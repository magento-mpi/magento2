<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Page_Block_HtmlTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @dataProvider getConfigValuesDataProvider
     */
    public function testGetPrintLogoUrl($configData, $returnValue)
    {
        $storeConfig = $this->getMock('Magento_Core_Model_Store_Config');
        $storeConfig->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValueMap($configData));

        $urlBuilder = $this->getMock('Magento_Core_Model_Url', array('getBaseUrl'));
        $urlBuilder->expects($this->any())
            ->method('getBaseUrl')
            ->will($this->returnValue('http://localhost/pub/media/'));

        $context = Mage::getModel('Magento_Core_Block_Template_Context', array(
            'storeConfig' => $storeConfig,
            'urlBuilder' => $urlBuilder,
        ));

        $block = Magento_Test_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Page_Block_Html', array('context' => $context));

        $this->assertEquals($returnValue, $block->getPrintLogoUrl());
    }

    public function getConfigValuesDataProvider()
    {
        return array(
            'sales_identity_logo_html' => array(
                array(array('sales/identity/logo_html', null, 'image.gif')),
                'http://localhost/pub/media/sales/store/logo_html/image.gif'
            ),
            'sales_identity_logo' => array(
                array(array('sales/identity/logo', null, 'image.gif')),
                'http://localhost/pub/media/sales/store/logo/image.gif'
            ),
            'sales_identity_logoTif' => array(
                array(array('sales/identity/logo', null, 'image.tif')),
                ''
            ),
            'sales_identity_logoTiff' => array(
                array(array('sales/identity/logo', null, 'image.tiff')),
                ''
            ),
            'no_logo' => array(
                array(),
                ''
            ),
        );
    }
}
