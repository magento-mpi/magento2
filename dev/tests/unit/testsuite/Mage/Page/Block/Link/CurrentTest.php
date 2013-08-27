<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Page_Block_Link_CurrentTest extends PHPUnit_Framework_TestCase
{
    public function testGetUrl()
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $path = 'test/path';
        $url = 'http://example.com/asdasd';

        $urlBuilder = $this->getMockForAbstractClass('Mage_Core_Model_UrlInterface');
        $urlBuilder->expects($this->once())->method('getUrl')->with($path)->will($this->returnValue($url));

        $context = $objectManagerHelper->getObject('Mage_Core_Block_Template_Context', array('urlBuilder' => $urlBuilder));
        $link = $objectManagerHelper->getObject(
            'Mage_Page_Block_Link_Current',
            array(
                'context' => $context,
            )
        );
        $link->setPath($path);
        $this->assertEquals($url, $link->getHref());
    }


    public function testIsCurrent()
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $path = 'test/path';
        $url = 'http://example.com/asdasd';

        $urlBuilder = $this->getMockForAbstractClass('Mage_Core_Model_UrlInterface');
        $urlBuilder->expects($this->any())->method('getUrl')->with($path)->will($this->returnValue($url));

        $context = $objectManagerHelper->getObject(
            'Mage_Core_Block_Template_Context',
            array('urlBuilder' => $urlBuilder)
        );
        $link = $objectManagerHelper->getObject(
            'Mage_Page_Block_Link_Current',
            array(
                'context' => $context,
            )
        );
        $link->setIsCurrent(true);
        $this->assertTrue($link->getIsCurrent());
    }
}
