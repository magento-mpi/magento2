<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_Apps_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    public function testGetAppTabUrl()
    {
        $locale = $this->getMockBuilder('Mage_Core_Model_Locale')->disableOriginalConstructor()->getMock();
        $locale->expects($this->once())->method('getLocaleCode')->will($this->returnValue('en_GB'));

        $config = $this->getMockBuilder('Mage_Core_Model_Config')->disableOriginalConstructor()->getMock();
        $config->expects($this->once())->method('getNode')
            ->with($this->equalTo(Saas_Apps_Helper_Data::XML_PATH_APP_TAB_URL . '/en_GB'))
            ->will($this->returnValue('https://golinks.magento.com/uk/app-tab'));

        $context = $this->getMockBuilder('Mage_Core_Helper_Context')->disableOriginalConstructor()->getMock();

        $data = new Saas_Apps_Helper_Data($config, $context, $locale);
        $this->assertEquals('https://golinks.magento.com/uk/app-tab', $data->getAppTabUrl());
    }

    public function testGetAppTabUrlNegative()
    {
        $locale = $this->getMockBuilder('Mage_Core_Model_Locale')->disableOriginalConstructor()->getMock();
        $locale->expects($this->once())->method('getLocaleCode')->will($this->returnValue('en_GB'));

        $config = $this->getMockBuilder('Mage_Core_Model_Config')->disableOriginalConstructor()->getMock();
        $config->expects($this->at(0))->method('getNode')
            ->with($this->equalTo(Saas_Apps_Helper_Data::XML_PATH_APP_TAB_URL . '/en_GB'))
            ->will($this->returnValue(''));
        $config->expects($this->at(1))->method('getNode')
            ->with($this->equalTo(Saas_Apps_Helper_Data::XML_PATH_APP_TAB_URL . '/en_US'))
            ->will($this->returnValue('https://golinks.magento.com/apps/admin'));

        $context = $this->getMockBuilder('Mage_Core_Helper_Context')->disableOriginalConstructor()->getMock();

        $data = new Saas_Apps_Helper_Data($config, $context, $locale);
        $this->assertEquals('https://golinks.magento.com/apps/admin', $data->getAppTabUrl());
    }
}
