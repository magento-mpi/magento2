<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rss\Helper;

class WishlistRssTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_customerSession = $this->_objectManager->create('Magento\Customer\Model\Session');
        $this->_coreData = $this->_objectManager->create('Magento\Core\Helper\Data');
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoAppArea frontend
     */
    public function testGetCustomer()
    {
        $fixtureCustomerId = 1;
        $this->_customerSession->loginById($fixtureCustomerId);

        /** @var \Magento\App\Helper\Context $contextHelper */
        $contextHelper = $this->_objectManager->create('Magento\App\Helper\Context');
        /** @var \Magento\App\Request\Http $request */
        $request = $contextHelper->getRequest();
        $request->setParam('data', $this->_coreData->urlEncode($fixtureCustomerId));

        /** @var \Magento\Rss\Helper\WishlistRss $wishlistHelper */
        $wishlistHelper = $this->_objectManager->create('Magento\Rss\Helper\WishlistRss',
            [
                'context' => $contextHelper,
                'customerSession' => $this->_customerSession
            ]
        );

        $this->assertEquals($this->_customerSession->getCustomerDataObject(), $wishlistHelper->getCustomer());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Wishlist/_files/wishlist_with_product_qty_increments.php
     * @magentoAppArea frontend
     */
    public function testGetWishlistByParam()
    {
        $fixtureCustomerId = 1;
        $this->_customerSession->loginById($fixtureCustomerId);

        /** @var \Magento\Wishlist\Model\Wishlist $wishlist */
        $wishlist = $this->_objectManager->create('Magento\Wishlist\Model\Wishlist')
            ->loadByCustomerId($fixtureCustomerId);
        $wishlist->load($wishlist->getId());

        /** @var \Magento\App\Helper\Context $contextHelper */
        $contextHelper = $this->_objectManager->create('Magento\App\Helper\Context');
        /** @var \Magento\App\Request\Http $request */
        $request = $contextHelper->getRequest();
        $request->setParam('wishlist_id', $wishlist->getId());
        $request->setParam('data', $this->_coreData->urlEncode($fixtureCustomerId));

        /** @var \Magento\Rss\Helper\WishlistRss $wishlistHelper */
        $wishlistHelper = $this->_objectManager->create('Magento\Rss\Helper\WishlistRss',
            [
                'context' => $contextHelper,
                'customerSession' => $this->_customerSession
            ]
        );

        $this->assertEquals($wishlist, $wishlistHelper->getWishlist());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Wishlist/_files/wishlist_with_product_qty_increments.php
     * @magentoAppArea frontend
     */
    public function testGetWishlistByCustomerId()
    {
        $fixtureCustomerId = 1;
        $this->_customerSession->loginById($fixtureCustomerId);

        /** @var \Magento\Wishlist\Model\Wishlist $wishlist */
        $wishlist = $this->_objectManager->create('Magento\Wishlist\Model\Wishlist')
            ->loadByCustomerId($fixtureCustomerId);

        /** @var \Magento\App\Helper\Context $contextHelper */
        $contextHelper = $this->_objectManager->create('Magento\App\Helper\Context');
        /** @var \Magento\App\Request\Http $request */
        $request = $contextHelper->getRequest();
        $request->setParam('wishlist_id', '');
        $request->setParam('data', $this->_coreData->urlEncode($fixtureCustomerId));

        /** @var \Magento\Rss\Helper\WishlistRss $wishlistHelper */
        $wishlistHelper = $this->_objectManager->create('Magento\Rss\Helper\WishlistRss',
            [
                'context' => $contextHelper,
                'customerSession' => $this->_customerSession
            ]
        );

        $this->assertEquals($wishlist, $wishlistHelper->getWishlist());
    }
}
 