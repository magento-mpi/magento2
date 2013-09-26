<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_MultipleWishlist_RssTest extends Magento_TestFramework_TestCase_ControllerAbstract
{
    /**
     * @magentoConfigFixture current_store rss/wishlist/active 1
     * @magentoDataFixture Magento/MultipleWishlist/_files/wishlist.php
     */
    public function testWishlistAction()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $wishlist = $objectManager->create('Magento_Wishlist_Model_Wishlist');
        $wishlist->load('fixture_unique_code', 'sharing_code');
        $this->getRequest()->setParam('wishlist_id', $wishlist->getId());
        $this->dispatch('rss/index/wishlist');
        $this->assertContains('<![CDATA[Simple Product]]>', $this->getResponse()->getBody());
    }
}
