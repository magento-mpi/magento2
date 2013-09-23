<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Magento/Catalog/controllers/_files/products.php
 */
class Magento_Catalog_Controller_Product_CompareTest extends Magento_TestFramework_TestCase_ControllerAbstract
{
    public function testAddAction()
    {
        $this->_requireVisitorWithNoProducts();

        $this->dispatch('catalog/product_compare/add/product/1?nocookie=1');

        /** @var $session Magento_Catalog_Model_Session */
        $session = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Catalog_Model_Session');
        $this->assertInstanceOf('Magento_Core_Model_Message_Success', $session->getMessages()->getLastAddedMessage());
        $this->assertContains('Simple Product 1 Name',
            (string)$session->getMessages()->getLastAddedMessage()->getText());

        $this->assertRedirect();

        $this->_assertCompareListEquals(array(1));
    }

    public function testIndexActionAddProducts()
    {
        $this->_requireVisitorWithNoProducts();

        $this->dispatch('catalog/product_compare/index/items/2');

        $this->assertRedirect($this->equalTo('http://localhost/index.php/catalog/product_compare/index/'));

        $this->_assertCompareListEquals(array(2));
    }

    public function testRemoveAction()
    {
        $this->_requireVisitorWithTwoProducts();

        $this->dispatch('catalog/product_compare/remove/product/2');

        /** @var $session Magento_Catalog_Model_Session */
        $session = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Catalog_Model_Session');
        $this->assertInstanceOf('Magento_Core_Model_Message_Success', $session->getMessages()->getLastAddedMessage());
        $this->assertContains('Simple Product 2 Name',
            (string)$session->getMessages()->getLastAddedMessage()->getText());

        $this->assertRedirect();

        $this->_assertCompareListEquals(array(1));
    }

    public function testIndexActionDisplay()
    {
        $this->_requireVisitorWithTwoProducts();

        $this->dispatch('catalog/product_compare/index');

        $responseBody = $this->getResponse()->getBody();

        $this->assertContains('Products Comparison List', $responseBody);

        $this->assertContains('simple_product_1', $responseBody);
        $this->assertContains('Simple Product 1 Name', $responseBody);
        $this->assertContains('Simple Product 1 Full Description', $responseBody);
        $this->assertContains('Simple Product 1 Short Description', $responseBody);
        $this->assertContains('$1,234.56', $responseBody);

        $this->assertContains('simple_product_2', $responseBody);
        $this->assertContains('Simple Product 2 Name', $responseBody);
        $this->assertContains('Simple Product 2 Full Description', $responseBody);
        $this->assertContains('Simple Product 2 Short Description', $responseBody);
        $this->assertContains('$987.65', $responseBody);
    }

    public function testClearAction()
    {
        $this->_requireVisitorWithTwoProducts();

        $this->dispatch('catalog/product_compare/clear');

        /** @var $session Magento_Catalog_Model_Session */
        $session = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Catalog_Model_Session');
        $this->assertInstanceOf('Magento_Core_Model_Message_Success', $session->getMessages()->getLastAddedMessage());

        $this->assertRedirect();

        $this->_assertCompareListEquals(array());
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple_xss.php
     */
    public function testRemoveActionProductNameXss()
    {
        $this->_prepareCompareListWithProductNameXss();
        $this->dispatch('catalog/product_compare/remove/product/1?nocookie=1');
        $messages = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Catalog_Model_Session')->getMessages()->getItems();
        $isProductNamePresent = false;
        foreach ($messages as $message) {
            if (strpos($message->getCode(), '&lt;script&gt;alert(&quot;xss&quot;);&lt;/script&gt;') !== false) {
                $isProductNamePresent = true;
            }
            $this->assertNotContains('<script>alert("xss");</script>', (string)$message->getCode());
        }
        $this->assertTrue($isProductNamePresent, 'Product name was not found in session messages');
    }

    protected function _prepareCompareListWithProductNameXss()
    {
        /** @var $visitor Magento_Log_Model_Visitor */
        $visitor = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Log_Model_Visitor');
        $visitor->setSessionId(md5(time()) . md5(microtime()))
            ->setLastVisitAt(now())
            ->save();
        /** @var $item Magento_Catalog_Model_Product_Compare_Item */
        $item = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Product_Compare_Item');
        $item->setVisitorId($visitor->getId())
            ->setProductId(1)
            ->save();
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Log_Model_Visitor')
            ->load($visitor->getId());
    }

    protected function _requireVisitorWithNoProducts()
    {
        /** @var $visitor Magento_Log_Model_Visitor */
        $visitor = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Log_Model_Visitor');
        $visitor->setSessionId(md5(time()) . md5(microtime()))
            ->setLastVisitAt(now())
            ->save();

        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Log_Model_Visitor')
            ->load($visitor->getId());

        $this->_assertCompareListEquals(array());
    }

    protected function _requireVisitorWithTwoProducts()
    {
        /** @var $visitor Magento_Log_Model_Visitor */
        $visitor = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Log_Model_Visitor');
        $visitor->setSessionId(md5(time()) . md5(microtime()))
            ->setLastVisitAt(now())
            ->save();

        /** @var $item Magento_Catalog_Model_Product_Compare_Item */
        $item = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Product_Compare_Item');
        $item->setVisitorId($visitor->getId())
            ->setProductId(1)
            ->save();

        /** @var $item Magento_Catalog_Model_Product_Compare_Item */
        $item = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Product_Compare_Item');
        $item->setVisitorId($visitor->getId())
            ->setProductId(2)
            ->save();

        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Log_Model_Visitor')
            ->load($visitor->getId());

        $this->_assertCompareListEquals(array(1, 2));
    }

    /**
     * Assert that current visitor has exactly expected products in compare list
     *
     * @param array $expectedProductIds
     */
    protected function _assertCompareListEquals(array $expectedProductIds)
    {
        /** @var $compareItems Magento_Catalog_Model_Resource_Product_Compare_Item_Collection */
        $compareItems = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Resource_Product_Compare_Item_Collection');
        $compareItems->useProductItem(true); // important
        $compareItems->setVisitorId(
            Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Log_Model_Visitor')->getId()
        );
        $actualProductIds = array();
        foreach ($compareItems as $compareItem) {
            /** @var $compareItem Magento_Catalog_Model_Product_Compare_Item */
            $actualProductIds[] = $compareItem->getProductId();
        }
        $this->assertEquals($expectedProductIds, $actualProductIds, "Products in current visitor's compare list.");
    }
}
