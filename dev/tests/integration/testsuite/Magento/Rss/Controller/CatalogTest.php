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

class Magento_Rss_Controller_CatalogTest extends Magento_TestFramework_TestCase_ControllerAbstract
{
    /**
     * @param string $action
     * @dataProvider actionNoFeedDataProvider
     */
    public function testActionsNoFeed($action)
    {
        $this->dispatch("rss/catalog/{$action}");
        $this->assertHeaderPcre('Http/1.1', '/^404 Not Found$/');
        $this->assertEquals('There was no RSS feed enabled.', $this->getResponse()->getBody());
    }

    /**
     * @return array
     */
    public function actionNoFeedDataProvider()
    {
        return array(array('new'), array('special'), array('salesrule'), array('category'));
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/products_new.php
     * @magentoConfigFixture current_store rss/catalog/new 1
     */
    public function testNewAction()
    {
        $this->dispatch('rss/catalog/newcatalog');
        $this->assertContains('New Product', $this->getResponse()->getBody());
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_special_price.php
     * @magentoConfigFixture current_store rss/catalog/special 1
     */
    public function testSpecialAction()
    {
        $this->dispatch('rss/catalog/special');
        $body = $this->getResponse()->getBody();
        $this->assertContains('$10.00', $body);
        $this->assertContains('$5.99', $body);
    }

    /**
     * @magentoConfigFixture current_store rss/catalog/salesrule 1
     */
    public function testSalesruleAction()
    {
        $this->dispatch('rss/catalog/salesrule');
        $this->assertHeaderPcre('Content-Type', '/text\/xml/');
        // to improve accuracy of the test, implement a fixture of a shopping cart price rule with a coupon
        $this->assertContains(
            '<link>http://localhost/index.php/rss/catalog/salesrule/</link>', $this->getResponse()->getBody()
        );
    }

    /**
     * @dataProvider authorizationFailedDataProvider
     */
    public function testAuthorizationFailed($action)
    {
        $this->dispatch("rss/catalog/{$action}");
        $this->assertHeaderPcre('Http/1.1', '/^401 Unauthorized$/');
    }

    /**
     * @return array
     */
    public function authorizationFailedDataProvider()
    {
        return array(
            array('notifystock'),
            array('review')
        );
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/multiple_products.php
     * @magentoConfigFixture current_store cataloginventory/item_options/notify_stock_qty 75
     */
    public function testNotifyStockAction()
    {
        // workaround: trigger updating "low stock date", because RSS collection requires it to be not null
        Mage::getResourceSingleton('Magento\CatalogInventory\Model\Resource\Stock')->updateLowStockDate();
        $this->_loginAdmin();
        $this->dispatch('rss/catalog/notifystock');

        $this->assertHeaderPcre('Content-Type', '/text\/xml/');

        // assert that among 2 products in fixture, there is only one with 50 qty
        $body = $this->getResponse()->getBody();
        $this->assertNotContains('<![CDATA[Simple Product]]>', $body); // this one was supposed to have qty 100 ( > 75)
        $this->assertContains('<![CDATA[Simple Product2]]>', $body); // 50 < 75
        $this->assertNotContains('<![CDATA[Simple Product 3]]>', $body);// this one was supposed to have qty 140 ( > 75)
    }

    /**
     * @magentoDataFixture Magento/Review/_files/reviews.php
     */
    public function testReviewAction()
    {
        $this->_loginAdmin();
        $this->dispatch('rss/catalog/review');
        $this->assertHeaderPcre('Content-Type', '/text\/xml/');
        $body = $this->getResponse()->getBody();
        $this->assertContains('"Simple Product 3"', $body);
        $this->assertContains('Review text', $body);
    }

    /**
     * @magentoConfigFixture current_store rss/catalog/category 1
     */
    public function testCategoryAction()
    {
        $this->getRequest()->setParam('cid', Mage::app()->getStore()->getRootCategoryId());
        $this->dispatch('rss/catalog/category');
        $this->assertStringMatchesFormat(
            '%A<link>http://localhost/index.php/catalog/category/view/%A/id/2/</link>%A',
            $this->getResponse()->getBody()
        );
    }

    /**
     * Emulate administrator logging in
     */
    protected function _loginAdmin()
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento\Core\Model\View\DesignInterface')
            ->setArea('adminhtml')
            ->setDefaultDesignTheme();
        $this->getRequest()->setServer(array(
            'PHP_AUTH_USER' => Magento_TestFramework_Bootstrap::ADMIN_NAME,
            'PHP_AUTH_PW' => Magento_TestFramework_Bootstrap::ADMIN_PASSWORD
        ));
    }
}
