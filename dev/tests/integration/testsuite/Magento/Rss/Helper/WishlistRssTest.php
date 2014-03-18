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

    /**
     * @var \Magento\App\Helper\Context
     */
    protected $_contextHelper;

    /**
     * @var \Magento\Rss\Helper\WishlistRss
     */
    protected $_wishlistHelper;

    /**
     * @var int
     */
    protected $_fixtureCustomerId;

    protected function setUp()
    {
        $this->_fixtureCustomerId = 1;

        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_customerSession = $this->_objectManager->create('Magento\Customer\Model\Session');
        $this->_coreData = $this->_objectManager->create('Magento\Core\Helper\Data');

        $this->_contextHelper = $this->_objectManager->create('Magento\App\Helper\Context');
        $request = $this->_contextHelper->getRequest();
        $request->setParam('data', $this->_coreData->urlEncode($this->_fixtureCustomerId));

        $this->_wishlistHelper = $this->_objectManager->create('Magento\Rss\Helper\WishlistRss',
            [
                'context' => $this->_contextHelper,
                'customerSession' => $this->_customerSession
            ]
        );

        $this->_customerSession->loginById($this->_fixtureCustomerId);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoAppArea frontend
     */
    public function testGetCustomer()
    {
        $this->assertEquals($this->_customerSession->getCustomerDataObject(), $this->_wishlistHelper->getCustomer());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Wishlist/_files/wishlist_with_product_qty_increments.php
     * @magentoAppArea frontend
     */
    public function testGetWishlistByParam()
    {
        /** @var \Magento\Wishlist\Model\Wishlist $wishlist */
        $wishlist = $this->_objectManager->create('Magento\Wishlist\Model\Wishlist')
            ->loadByCustomerId($this->_fixtureCustomerId);
        $wishlist->load($wishlist->getId());

        /** @var \Magento\App\Request\Http $request */
        $request = $this->_contextHelper->getRequest();
        $request->setParam('wishlist_id', $wishlist->getId());

        $this->assertEquals($wishlist, $this->_wishlistHelper->getWishlist());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Wishlist/_files/wishlist_with_product_qty_increments.php
     * @magentoAppArea frontend
     */
    public function testGetWishlistByCustomerId()
    {
        /** @var \Magento\Wishlist\Model\Wishlist $wishlist */
        $wishlist = $this->_objectManager->create('Magento\Wishlist\Model\Wishlist')
            ->loadByCustomerId($this->_fixtureCustomerId);

        /** @var \Magento\App\Request\Http $request */
        $request = $this->_contextHelper->getRequest();
        $request->setParam('wishlist_id', '');

        $this->assertEquals($wishlist, $this->_wishlistHelper->getWishlist());
    }
}
 