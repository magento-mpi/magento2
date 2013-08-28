<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Wishlist_RssTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @magentoConfigFixture current_store rss/wishlist/active 1
     * @magentoDataFixture Enterprise/Wishlist/_files/wishlist.php
     */
    public function testWishlistAction()
    {
        $wishlist = Mage::getModel('Magento_Wishlist_Model_Wishlist');
        $wishlist->load('fixture_unique_code', 'sharing_code');
        $this->getRequest()->setParam('wishlist_id', $wishlist->getId());
        $this->dispatch('rss/index/wishlist');
        $this->assertContains('<![CDATA[Simple Product]]>', $this->getResponse()->getBody());
    }
}
