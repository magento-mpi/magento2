<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Helper;

use Magento\Sales\Model\Quote\Item;

class CartTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param integer $id
     * @param string $url
     * @param bool $isAjax
     * @param string $expectedPostData
     *
     * @dataProvider deletePostJsonDataProvider
     */
    public function testGetDeletePostJson($id, $url, $isAjax, $expectedPostData)
    {
        $storeManager = $this->getMockForAbstractClass('\Magento\Store\Model\StoreManagerInterface');
        $coreData = $this->getMock('\Magento\Core\Helper\Data', array(), array(), '', false);
        $scopeConfig = $this->getMockForAbstractClass('\Magento\Framework\App\Config\ScopeConfigInterface');
        $checkoutCart = $this->getMock('\Magento\Checkout\Model\Cart', array(), array(), '', false);
        $checkoutSession = $this->getMock('\Magento\Checkout\Model\Session', array(), array(), '', false);

        $context = $this->getMock('\Magento\Framework\App\Helper\Context', array(), array(), '', false);
        $urlBuilder = $this->getMock('Magento\Framework\UrlInterface');
        $context->expects($this->once())
            ->method('getUrlBuilder')
            ->will($this->returnValue($urlBuilder));


        $item = $this->getMock('Magento\Sales\Model\Quote\Item', array(), array(), '', false);
        $request = $this->getMock('\Magento\Framework\App\Request\Http', array(), array(), '', false);
        $context->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $helper = new Cart(
            $context,
            $storeManager,
            $coreData,
            $scopeConfig,
            $checkoutCart,
            $checkoutSession
        );

        $item->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($id));

        $request->expects($this->once())
            ->method('isAjax')
            ->will($this->returnValue($isAjax));

        $urlBuilder->expects($this->any())
            ->method('getCurrentUrl')
            ->will($this->returnValue($url));

        $urlBuilder->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue($url));

        $result = $helper->getDeletePostJson($item);
        $this->assertEquals($expectedPostData, $result);
    }

    /**
     * @return array
     */
    public function deletePostJsonDataProvider()
    {
        $url = 'http://localhost.com/dev/checkout/cart/delete/';
        $uenc = strtr(base64_encode($url), '+/=', '-_,');
        $id = 1;
        $expectedPostData1 = json_encode(
            array(
                'action' => $url,
                'data' => array('id' => $id, 'uenc' => $uenc)
            )
        );
        $expectedPostData2 = json_encode(
            array(
                'action' => $url,
                'data' => array('id' => $id)
            )
        );

        return array(
            array($id, $url, false, $expectedPostData1),
            array($id, $url, true, $expectedPostData2),
        );
    }
}
