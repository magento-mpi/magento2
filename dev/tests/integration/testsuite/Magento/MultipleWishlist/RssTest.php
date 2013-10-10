<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\MultipleWishlist;

class RssTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @magentoConfigFixture current_store rss/wishlist/active 1
     * @magentoDataFixture Magento/MultipleWishlist/_files/wishlist.php
     */
    public function testWishlistAction()
    {
        $wishlist = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Wishlist\Model\Wishlist');
        $wishlist->load('fixture_unique_code', 'sharing_code');
        $this->getRequest()->setParam('wishlist_id', $wishlist->getId());
        $this->dispatch('rss/index/wishlist');
        $this->assertContains('<![CDATA[Simple Product]]>', $this->getResponse()->getBody());
    }
}
