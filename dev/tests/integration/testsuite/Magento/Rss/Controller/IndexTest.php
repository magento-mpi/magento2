<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rss\Controller;

class IndexTest extends \Magento\TestFramework\TestCase\AbstractController
{
    public function testIndexActionDisabled()
    {
        $this->dispatch('rss/index/index');
        $this->assert404NotFound();
    }

    /**
     * @magentoConfigFixture current_store rss/config/active 1
     * @magentoConfigFixture current_store rss/catalog/new 1
     */
    public function testIndexAction()
    {
        $this->dispatch('rss/index/index');
        $this->assertContains('/rss/catalog/new/', $this->getResponse()->getBody());
    }

    public function testNofeedAction()
    {
        $this->dispatch('rss/index/nofeed');
        $this->assertHeaderPcre('Status', '/404/');
        $this->assertHeaderPcre('Content-Type', '/text\/plain/');
    }

    /**
     * @magentoConfigFixture current_store rss/wishlist/active 1
     * @magentoDataFixture Magento/Wishlist/_files/wishlist.php
     * @magentoAppIsolation enabled
     */
    public function testWishlistAction()
    {
        $wishlist = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Wishlist\Model\Wishlist'
        );
        $wishlist->load('fixture_unique_code', 'sharing_code');
        $this->getRequest()->setParam('wishlist_id', $wishlist->getId())->setParam('data', base64_encode('1'));
        $session = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Customer\Model\Session');
        $service = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Customer\Service\V1\CustomerAccountService'
        );
        $customer = $service->authenticate('customer@example.com', 'password');
        $session->setCustomerDataAsLoggedIn($customer);

        $this->dispatch('rss/index/wishlist');
        $this->assertContains('<![CDATA[Simple Product]]>', $this->getResponse()->getBody());
    }
}
