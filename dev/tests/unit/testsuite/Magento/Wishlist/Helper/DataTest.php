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
        $encoded = 'encodedUrl';

        $coreData = $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false);
        $coreData->expects($this->any())
            ->method('urlEncode')
            ->with($url)
            ->will($this->returnValue($encoded));

        $store = $this->getMock('Magento\Store\Model\Store', array(), array(), '', false);
        $store->expects($this->any())
            ->method('getUrl')
            ->with('wishlist/index/cart', array('item' => '%item%', 'uenc' => $encoded))
            ->will($this->returnValue($url));

        $storeManager = $this->getMock('Magento\Framework\StoreManagerInterface', array(), array(), '', false);
        $storeManager->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($store));

        $urlBuilder = $this->getMock('Magento\Framework\UrlInterface\Proxy', array('getUrl'), array(), '', false);
        $urlBuilder->expects($this->any())
            ->method('getUrl')
            ->with('*/*/*', array('_current' => true, '_use_rewrite' => true, '_scope_to_url' => true))
            ->will($this->returnValue($url));

        $context = $this->getMock('Magento\Framework\App\Helper\Context', array(), array(), '', false);
        $context->expects($this->once())
            ->method('getUrlBuilder')
            ->will($this->returnValue($urlBuilder));

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        /** @var \Magento\Wishlist\Helper\Data $wishlistHelper */
        $wishlistHelper = $objectManager->getObject(
            'Magento\Wishlist\Helper\Data',
            array('context' => $context, 'storeManager' => $storeManager, 'coreData' => $coreData)
        );

        $this->assertEquals($url, $wishlistHelper->getAddToCartUrl('%item%'));
    }
}
