<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Page_Block_Link_CurrentTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_Helper_ObjectManager
     */
    protected $_objectManagerHelper;

    protected function setUp()
    {
        $this->_objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
    }

    public function testGetUrl()
    {
        $path = 'test/path';
        $url = 'http://example.com/asdasd';

        /** @var  Mage_Core_Block_Template_Context $context */
        $context = $this->_objectManagerHelper->getObject('Mage_Core_Block_Template_Context');
        $urlBuilder = $context->getUrlBuilder();
        $urlBuilder->expects($this->once())->method('getUrl')->with($path)->will($this->returnValue($url));

        $link = $this->_objectManagerHelper->getObject(
            'Mage_Page_Block_Link_Current',
            array(
                'context' => $context,
            )
        );
        $link->setPath($path);
        $this->assertEquals($url, $link->getHref());
    }


    public function testIsCurrentIfIsset()
    {
        $link = $this->_objectManagerHelper->getObject('Mage_Page_Block_Link_Current');
        $link->setCurrent(true);
        $this->assertTrue($link->IsCurrent());
    }

    public function testIsCurrent()
    {
        $path = 'test/path';
        $url = 'http://example.com/a/b';

        /** @var  Mage_Core_Block_Template_Context $context */
        $context = $this->_objectManagerHelper->getObject('Mage_Core_Block_Template_Context');

        $request = $context->getRequest();
        $request->expects($this->once())->method('getModuleName')->will($this->returnValue('a'));
        $request->expects($this->once())->method('getControllerName')->will($this->returnValue('b'));
        $request->expects($this->once())->method('getActionName')->will($this->returnValue('d'));

        $context->getFrontController()->expects($this->once())->method('getDefault')
            ->will($this->returnValue(array('action' => 'd')));

        $urlBuilder = $context->getUrlBuilder();
        $urlBuilder->expects($this->at(0))->method('getUrl')->with($path)->will($this->returnValue($url));
        $urlBuilder->expects($this->at(1))->method('getUrl')->with('a/b')->will(
            $this->returnValue($url)
        );

        /** @var Mage_Page_Block_Link_Current $link */
        $link = $this->_objectManagerHelper->getObject(
            'Mage_Page_Block_Link_Current',
            array(
                'context' => $context,
            )
        );
        $link->setPath($path);
        $this->assertTrue($link->isCurrent());
    }

    public function testIsCurrentFalse()
    {
        /** @var  Mage_Core_Block_Template_Context $context */
        $context = $this->_objectManagerHelper->getObject('Mage_Core_Block_Template_Context');

        $urlBuilder = $context->getUrlBuilder();
        $urlBuilder->expects($this->at(0))->method('getUrl')->will($this->returnValue('1'));
        $urlBuilder->expects($this->at(1))->method('getUrl')->will($this->returnValue('2'));

        /** @var Mage_Page_Block_Link_Current $link */
        $link = $this->_objectManagerHelper->getObject(
            'Mage_Page_Block_Link_Current',
            array(
                'context' => $context,
            )
        );
        $this->assertFalse($link->isCurrent());
    }
}
