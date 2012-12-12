<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Page
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Page_Block_Html_HeaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Mage_Page_Block_Html_Header::getLogoSrc
     */
    public function testGetLogoSrc()
    {
        $storeConfig = $this->getMock('Mage_Core_Model_Store_Config', array('getConfig'));
        $storeConfig->expects($this->once())
            ->method('getConfig')
            ->will($this->returnValue('default/image.gif'));

        $urlBuilder = $this->getMock('Mage_Core_Model_Url', array('getBaseUrl'));
        $urlBuilder->expects($this->once())
            ->method('getBaseUrl')
            ->will($this->returnValue('http://localhost/pub/media/'));

        $configOptions = $this->getMock('Mage_Core_Model_Config_Options', array('getDir'));
        $configOptions->expects($this->once())
            ->method('getDir')
            ->will($this->returnValue(__DIR__ . DIRECTORY_SEPARATOR . '_files'));

        $helper = $this->getMock('Mage_Core_Helper_File_Storage_Database', array('checkDbUsage'));
        $helper->expects($this->once())
            ->method('checkDbUsage')
            ->will($this->returnValue(false));

        $helperFactory = $this->getMock('Mage_Core_Model_Factory_Helper', array('get'));
        $helperFactory->expects($this->once())
            ->method('get')
            ->will($this->returnValue($helper));

        $objectManager = new Magento_Test_Helper_ObjectManager($this);

        $arguments = array(
            'storeConfig' => $storeConfig,
            'urlBuilder' => $urlBuilder,
            'configOptions' => $configOptions,
            'helperFactory' => $helperFactory
        );
        $this->_block = $objectManager->getBlock('Mage_Page_Block_Html_Header', $arguments);

        $this->assertEquals('http://localhost/pub/media/logo/default/image.gif', $this->_block->getLogoSrc());
    }
}
