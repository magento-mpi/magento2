<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Helper\Product;

/**
 * Class CompareTest
 * @package Magento\Catalog\Helper\Product
 */
class CompareTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Helper\Product\Compare
     */
    protected $compareHelper;

    /**
     * @var \Magento\Framework\App\Helper\Context | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \Magento\Url | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Core\Helper\Data | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $postDataHelper;

    /**
     * @var \Magento\Framework\App\Request\Http | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    public function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->urlBuilder = $this->getMock('Magento\Url', array('getUrl'), array(), '', false);
        $this->request = $this->getMock('Magento\Framework\App\Request\Http', array('getServer'), array(), '', false);
        /** @var \Magento\Framework\App\Helper\Context $context */
        $this->context = $this->getMock(
            'Magento\Framework\App\Helper\Context',
            array('getUrlBuilder', 'getRequest'),
            array(),
            '',
            false
        );
        $this->context->expects($this->once())
            ->method('getUrlBuilder')
            ->will($this->returnValue($this->urlBuilder));
        $this->context->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($this->request));
        $this->postDataHelper = $this->getMock(
            'Magento\Core\Helper\PostData',
            array('getPostData'),
            array(),
            '',
            false
        );

        $this->compareHelper = $objectManager->getObject(
            'Magento\Catalog\Helper\Product\Compare',
            array('context' => $this->context, 'coreHelper' => $this->postDataHelper)
        );
    }

    public function testGetPostDataRemove()
    {
        //Data
        $productId = 1;
        $removeUrl = 'catalog/product_compare/remove';
        $compareListUrl = 'catalog/product_compare';
        $postParams = array(
            \Magento\Framework\App\Action\Action::PARAM_NAME_URL_ENCODED => $this->compareHelper
                ->urlEncode($compareListUrl),
            'product' => $productId
        );

        //Verification
        $this->urlBuilder->expects($this->at(0))
            ->method('getUrl')
            ->with($compareListUrl)
            ->will($this->returnValue($compareListUrl));
        $this->urlBuilder->expects($this->at(1))
            ->method('getUrl')
            ->with($removeUrl)
            ->will($this->returnValue($removeUrl));
        $this->postDataHelper->expects($this->once())
            ->method('getPostData')
            ->with($removeUrl, $postParams)
            ->will($this->returnValue(true));

        /** @var \Magento\Catalog\Model\Product | \PHPUnit_Framework_MockObject_MockObject $product */
        $product = $this->getMock('Magento\Catalog\Model\Product', array('getId', '__wakeup'), array(), '', false);
        $product->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($productId));

        $this->assertTrue($this->compareHelper->getPostDataRemove($product));
    }

    public function testGetClearListUrl()
    {
        //Data
        $url = 'catalog/product_compare/clear';

        //Verification
        $this->urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with($url)
            ->will($this->returnValue($url));

        $this->assertEquals($url, $this->compareHelper->getClearListUrl());
    }

    public function testGetPostDataClearList()
    {
        //Data
        $refererUrl = 'home/';
        $clearUrl = 'catalog/product_compare/clear';
        $postParams = array(
            \Magento\Framework\App\Action\Action::PARAM_NAME_URL_ENCODED => $this->compareHelper->urlEncode($refererUrl)
        );

        //Verification
        $this->request->expects($this->once())
            ->method('getServer')
            ->with('HTTP_REFERER')
            ->will($this->returnValue($refererUrl));

        $this->urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with($clearUrl)
            ->will($this->returnValue($clearUrl));

        $this->postDataHelper->expects($this->once())
            ->method('getPostData')
            ->with($clearUrl, $postParams)
            ->will($this->returnValue(true));

        $this->assertTrue($this->compareHelper->getPostDataClearList());
    }
}
