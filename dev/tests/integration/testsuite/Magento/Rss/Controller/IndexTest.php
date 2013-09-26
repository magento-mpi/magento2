<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Rss_Controller_IndexTest extends Magento_TestFramework_TestCase_ControllerAbstract
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
        $wishlist = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Wishlist_Model_Wishlist');
        $wishlist->load('fixture_unique_code', 'sharing_code');
        $this->getRequest()->setParam('wishlist_id', $wishlist->getId())
            ->setParam('data', base64_encode('1'))
        ;
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Customer_Model_Session')
            ->login('customer@example.com', 'password');
        $this->dispatch('rss/index/wishlist');
        $this->assertContains('<![CDATA[Simple Product]]>', $this->getResponse()->getBody());
    }
}
