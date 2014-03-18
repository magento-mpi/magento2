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
     * @var \Magento\ObjectManager
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

        $wishlistHelper = $this->_objectManager->create('Magento\MultipleWishlist\Helper\Rss',
            [
                'context' => $contextHelper,
                'customerSession' => $this->_customerSession
            ]
        );

        /** @var \Magento\MultipleWishlist\Block\Rss $block */
        $block = $this->_objectManager->create('Magento\MultipleWishlist\Block\Rss',
            [
                'context' => $contextBlock,
                'customerSession' => $this->_customerSession,
                'wishlistHelper' => $wishlistHelper
            ]
        );
        /** @var \Magento\Escaper $escaper */
        $escaper = $this->_objectManager->create('Magento\Escaper');

        $expectedSting = sprintf("%%A<title><![CDATA[%s %s's Wish List]]></title>%%A",
            $escaper->escapeHtml($this->_customerSession->getCustomerDataObject()->getFirstname()),
            $escaper->escapeHtml($this->_customerSession->getCustomerDataObject()->getLastname())
        );
        $this->assertStringMatchesFormat($expectedSting, $block->toHtml());
    }
}
 