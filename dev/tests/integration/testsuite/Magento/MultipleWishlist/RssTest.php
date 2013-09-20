<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Magento_MultipleWishlist
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist;

class RssTest extends \Magento\TestFramework\TestCase\ControllerAbstract
{
    /**
     * @magentoConfigFixture current_store rss/wishlist/active 1
     * @magentoDataFixture Magento/MultipleWishlist/_files/wishlist.php
     */
    public function testWishlistAction()
    {
        $wishlist = \Mage::getModel('Magento\Wishlist\Model\Wishlist');
        $wishlist->load('fixture_unique_code', 'sharing_code');
        $this->getRequest()->setParam('wishlist_id', $wishlist->getId());
        $this->dispatch('rss/index/wishlist');
        $this->assertContains('<![CDATA[Simple Product]]>', $this->getResponse()->getBody());
    }
}
