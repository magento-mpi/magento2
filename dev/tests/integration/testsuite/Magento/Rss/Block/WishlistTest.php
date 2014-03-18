<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rss\Block;

class WishlistTest extends \PHPUnit_Framework_TestCase
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
     * @magentoDataFixture Magento/Wishlist/_files/wishlist_with_product_qty_increments.php
     * @magentoAppArea frontend
     */
    public function testCustomerTitle()
    {
        $fixtureCustomerId = 1;
        $this->_customerSession->loginById($fixtureCustomerId);

        /** @var \Magento\Wishlist\Model\Wishlist $wishlist */
        $wishlist = $this->_objectManager->create('Magento\Wishlist\Model\Wishlist')
            ->loadByCustomerId($fixtureCustomerId);
        /** @var \Magento\View\Element\Template\Context $context */
        $contextBlock = $this->_objectManager->create('Magento\View\Element\Template\Context');
        /** @var \Magento\App\Request\Http $request */
        $request = $contextBlock->getRequest();
        $request->setParam('wishlist_id', $wishlist->getId());
        $request->setParam('data', $this->_coreData->urlEncode($fixtureCustomerId));

        /** @var \Magento\App\Helper\Context $contextHelper */
        $contextHelper = $this->_objectManager->create('Magento\App\Helper\Context',
            ['httpRequest' =>  $request]
        );

        $wishlistHelper = $this->_objectManager->create('Magento\Rss\Helper\WishlistRss',
            [
                'context' => $contextHelper,
                'customerSession' => $this->_customerSession
            ]
        );

        /** @var \Magento\Rss\Block\Wishlist $block */
        $block = $this->_objectManager->create('Magento\Rss\Block\Wishlist',
            [
                'context' => $contextBlock,
                'customerSession' => $this->_customerSession,
                'wishlistHelper' => $wishlistHelper
            ]
        );
        $matches = [];
        preg_match('/<title>(.*)<\/title>/', $block->toHtml(), $matches);
        $this->assertEquals("<title><![CDATA[Firstname Lastname's Wishlist]]></title>", $matches[0]);
    }
}
 