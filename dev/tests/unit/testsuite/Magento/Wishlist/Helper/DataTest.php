<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\Wishlist\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAddToCartUrl()
    {
        $url = 'http://magento.com/wishlist/index/index/wishlist_id/1/?___store=default';
        $encoded = strtr(base64_encode($url), '+/=', '-_,');

        $coreData = $this->getMock('Magento\Core\Helper\Data', array('urlEncode'), array(), '', false);
        $coreData->expects($this->any())
            ->method('urlEncode')
            ->with($url)
            ->will($this->returnValue($encoded));

        $store = $this->getMock('Magento\Core\Model\Store', array('getUrl','__wakeup'), array(), '', false);
        $store->expects($this->any())
            ->method('getUrl')
            ->with(
                'wishlist/index/cart',
                array(
                    'item' => '%item%',
                    'uenc' => $encoded
                )
            )
            ->will($this->returnValue($url));

        $storeManager = $this->getMock('Magento\Core\Model\StoreManager', array('getStore'), array(), '', false);
        $storeManager->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($store));

        $urlBuilder = $this->getMock('Magento\UrlInterface\Proxy', array('getUrl'), array(), '', false);
        $urlBuilder->expects($this->any())
            ->method('getUrl')
            ->with(
                '*/*/*',
                array(
                    '_current' => true,
                    '_use_rewrite' => true,
                    '_scope_to_url' => true,
                    '_scope' => $store
                )
            )
            ->will($this->returnValue($url));

        $context = $this->getMock('Magento\App\Helper\Context', array('getUrlBuilder'), array(), '', false);
        $context->expects($this->once())
            ->method('getUrlBuilder')
            ->will($this->returnValue($urlBuilder));

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        /** @var \Magento\Wishlist\Helper\Data $wishlistHelper */
        $wishlistHelper = $objectManager->getObject(
            'Magento\Wishlist\Helper\Data',
            array('context' => $context, 'storeManager' => $storeManager, 'coreData' => $coreData)
        );

        $this->assertEquals($wishlistHelper->getAddToCartUrl('%item%'), $url);
    }
}