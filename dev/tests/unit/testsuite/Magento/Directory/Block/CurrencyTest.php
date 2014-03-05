<?php
namespace Magento\Directory\Block;

class CurrencyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Directory\Block\Currency
     */
    protected $object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $postDataHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlBuilder;

    public function setUp()
    {
        $this->urlBuilder = $this->getMock('\Magento\UrlInterface\Proxy', array('getUrl'), array(), '', false);
        $this->urlBuilder->expects($this->any())->method('getUrl')->will($this->returnArgument(0));

        /** @var \Magento\View\Element\Template\Context $context */
        $context = $this->getMock('\Magento\View\Element\Template\Context', array('getUrlBuilder'), array(), '', false);
        $context->expects($this->any())->method('getUrlBuilder')->will($this->returnValue($this->urlBuilder));

        /** @var \Magento\Directory\Model\CurrencyFactory $currencyFactory */
        $currencyFactory = $this->getMock('\Magento\Directory\Model\CurrencyFactory', array(), array(), '', false);
        $this->postDataHelper = $this->getMock('\Magento\Core\Helper\PostData', array(), array(), '', false);

        $this->object = new Currency(
            $context, $currencyFactory, $this->postDataHelper
        );
    }

    public function testGetSwitchCurrencyPostData()
    {
        $expectedResult = 'post_data';
        $expectedCurrencyCode = 'test';
        $switchUrl = 'directory/currency/switch';

        $this->postDataHelper->expects($this->once())
            ->method('getPostData')
            ->with($this->equalTo($switchUrl), $this->equalTo(['currency' => $expectedCurrencyCode]))
            ->will($this->returnValue($expectedResult));

        $this->assertEquals($expectedResult, $this->object->getSwitchCurrencyPostData($expectedCurrencyCode));
    }
}
