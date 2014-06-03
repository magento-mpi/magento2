<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Block;

class RssTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData;

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

        /** @var \Magento\Framework\App\Helper\Context $contextHelper */
        $contextHelper = $this->_objectManager->create('Magento\Framework\App\Helper\Context');

        $wishlistHelper = $this->_objectManager->create('Magento\MultipleWishlist\Helper\Rss',
            [
                'context' => $contextHelper,
                'customerSession' => $this->_customerSession
            ]
        );

        /** @var \Magento\Catalog\Block\Product\Context $context */
        $contextBlock = $this->_objectManager->create(
            'Magento\Catalog\Block\Product\Context',
            [
                'request' => $contextHelper->getRequest(),
                'wishlistHelper' => $wishlistHelper
            ]
        );
        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $contextBlock->getRequest();
        $request->setParam('wishlist_id', $wishlist->getId());
        $request->setParam('data', $this->_coreData->urlEncode($fixtureCustomerId));



        /** @var \Magento\MultipleWishlist\Block\Rss $block */
        $block = $this->_objectManager->create('Magento\MultipleWishlist\Block\Rss',
            [
                'context' => $contextBlock,
                'wishlistHelper' => $wishlistHelper
            ]
        );
        /** @var \Magento\Framework\Escaper $escaper */
        $escaper = $this->_objectManager->create('Magento\Framework\Escaper');

        $expectedSting = '%A' . __("<title><![CDATA[%1 %2's Wish List]]></title>",
            $escaper->escapeHtml($this->_customerSession->getCustomerDataObject()->getFirstname()),
            $escaper->escapeHtml($this->_customerSession->getCustomerDataObject()->getLastname())
        ) . '%A';
        $this->assertStringMatchesFormat($expectedSting, $block->toHtml());
    }
}
 