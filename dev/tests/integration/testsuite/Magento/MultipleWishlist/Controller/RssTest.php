<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\MultipleWishlist\Controller;

class RssTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @magentoConfigFixture current_store rss/wishlist/active 1
     * @magentoConfigFixture current_store wishlist/general/multiple_enabled 1
     * @magentoDataFixture Magento/MultipleWishlist/_files/wishlist.php
     */
    public function testWishlistAction()
    {
        $wishlist = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Wishlist\Model\Wishlist'
        );
        $wishlist->load('fixture_unique_code', 'sharing_code');
        $this->getRequest()->setParam('wishlist_id', $wishlist->getId());

        $session = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Customer\Model\Session');
        $service = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Customer\Service\V1\CustomerAccountService'
        );
        $customer = $service->authenticate('customer@example.com', 'password');
        $session->setCustomerDataAsLoggedIn($customer);

        $this->dispatch('wishlist/index/rss');
        $this->assertContains('<![CDATA[Simple Product]]>', $this->getResponse()->getBody());
    }
}
