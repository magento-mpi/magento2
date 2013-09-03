<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Page_Block_Html_HeaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Magento_Page_Block_Html_Header::getLogoSrc
     */
    public function testGetLogoSrc()
    {
        $storeConfig = $this->getMock('Magento_Core_Model_Store_Config', array('getConfig'));
        $storeConfig->expects($this->once())
            ->method('getConfig')
            ->will($this->returnValue('default/image.gif'));

        $urlBuilder = $this->getMock('Magento_Core_Model_Url', array('getBaseUrl'), array(), '', false);
        $urlBuilder->expects($this->once())
            ->method('getBaseUrl')
            ->will($this->returnValue('http://localhost/pub/media/'));

        $helper = $this->getMock('Magento_Core_Helper_File_Storage_Database',
            array('checkDbUsage'), array(), '', false, false
        );
        $helper->expects($this->once())
            ->method('checkDbUsage')
            ->will($this->returnValue(false));

        $helperFactory = $this->getMock('Magento_Core_Model_Factory_Helper', array('get'));
        $helperFactory->expects($this->once())
            ->method('get')
            ->will($this->returnValue($helper));

        $dirsMock = $this->getMock('Magento_Core_Model_Dir', array('getDir'), array(), '', false);
        $dirsMock->expects($this->any())
            ->method('getDir')
            ->with(Magento_Core_Model_Dir::MEDIA)
            ->will($this->returnValue(__DIR__ . DIRECTORY_SEPARATOR . '_files'));

        $objectManager = new Magento_Test_Helper_ObjectManager($this);

        $arguments = array(
            'storeConfig' => $storeConfig,
            'urlBuilder' => $urlBuilder,
            'helperFactory' => $helperFactory,
            'dirs' => $dirsMock
        );
        $block = $objectManager->getObject('Magento_Page_Block_Html_Header', $arguments);

        $this->assertEquals('http://localhost/pub/media/logo/default/image.gif', $block->getLogoSrc());
    }
}
