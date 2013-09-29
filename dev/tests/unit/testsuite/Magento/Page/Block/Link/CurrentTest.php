<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Page\Block\Link;

class CurrentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManagerHelper;

    protected function setUp()
    {
        $this->_objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    public function testGetUrl()
    {
        $path = 'test/path';
        $url = 'http://example.com/asdasd';

        /** @var  \Magento\Core\Block\Template\Context $context */
        $context = $this->_objectManagerHelper->getObject('Magento\Core\Block\Template\Context');
        $urlBuilder = $context->getUrlBuilder();
        $urlBuilder->expects($this->once())->method('getUrl')->with($path)->will($this->returnValue($url));

        $link = $this->_objectManagerHelper->getObject(
            'Magento\Page\Block\Link\Current',
            array(
                'context' => $context,
            )
        );
        $link->setPath($path);
        $this->assertEquals($url, $link->getHref());
    }


    public function testIsCurrentIfIsset()
    {
        $link = $this->_objectManagerHelper->getObject('Magento\Page\Block\Link\Current');
        $link->setCurrent(true);
        $this->assertTrue($link->IsCurrent());
    }

    public function testIsCurrent()
    {
        $path = 'test/path';
        $url = 'http://example.com/a/b';

        /** @var  \Magento\Core\Block\Template\Context $context */
        $context = $this->_objectManagerHelper->getObject('Magento\Core\Block\Template\Context');

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

        /** @var \Magento\Page\Block\Link\Current $link */
        $link = $this->_objectManagerHelper->getObject(
            'Magento\Page\Block\Link\Current',
            array(
                'context' => $context,
            )
        );
        $link->setPath($path);
        $this->assertTrue($link->isCurrent());
    }

    public function testIsCurrentFalse()
    {
        /** @var  \Magento\Core\Block\Template\Context $context */
        $context = $this->_objectManagerHelper->getObject('Magento\Core\Block\Template\Context');

        $urlBuilder = $context->getUrlBuilder();
        $urlBuilder->expects($this->at(0))->method('getUrl')->will($this->returnValue('1'));
        $urlBuilder->expects($this->at(1))->method('getUrl')->will($this->returnValue('2'));

        /** @var \Magento\Page\Block\Link\Current $link */
        $link = $this->_objectManagerHelper->getObject(
            'Magento\Page\Block\Link\Current',
            array(
                'context' => $context,
            )
        );
        $this->assertFalse($link->isCurrent());
    }
}
