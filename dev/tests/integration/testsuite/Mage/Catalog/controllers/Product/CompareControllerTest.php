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
 * Test class for Mage_Catalog_Product_CompareController.
 *
 * @magentoDataFixture Mage/Catalog/controllers/_files/products.php
 */
class Mage_Catalog_Product_CompareControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    protected function _requireVisitorWithNoProducts()
    {
        $visitor = new Mage_Log_Model_Visitor;
        $visitor->setSessionId(md5(time()) . md5(microtime()))
            ->setLastVisitAt(now())
            ->save();

        Mage::getSingleton('Mage_Log_Model_Visitor')->load($visitor->getId());

        $this->_assertCompareListEquals(array());
    }

    protected function _requireVisitorWithTwoProducts()
    {
        $visitor = new Mage_Log_Model_Visitor;
        $visitor->setSessionId(md5(time()) . md5(microtime()))
            ->setLastVisitAt(now())
            ->save();

        $item = new Mage_Catalog_Model_Product_Compare_Item;
        $item->setVisitorId($visitor->getId())
            ->setProductId(1)
            ->save();

        $item = new Mage_Catalog_Model_Product_Compare_Item;
        $item->setVisitorId($visitor->getId())
            ->setProductId(2)
            ->save();

        Mage::getSingleton('Mage_Log_Model_Visitor')->load($visitor->getId());

        $this->_assertCompareListEquals(array(1, 2));
    }

    /**
     * Assert that current visitor has exactly expected products in compare list
     *
     * @param array $expectedProductIds
     */
    protected function _assertCompareListEquals(array $expectedProductIds)
    {
        $compareItems = new Mage_Catalog_Model_Resource_Product_Compare_Item_Collection;
        $compareItems->useProductItem(true); // important
        $compareItems->setVisitorId(
            Mage::getSingleton('Mage_Log_Model_Visitor')->getId()
        );
        $actualProductIds = array();
        foreach ($compareItems as $compareItem) {
            /** @var $compareItem Mage_Catalog_Model_Product_Compare_Item */
            $actualProductIds[] = $compareItem->getProductId();
        }
        $this->assertEquals($expectedProductIds, $actualProductIds, "Products in current visitor's compare list.");
    }

    public function testAddAction()
    {
        $this->_requireVisitorWithNoProducts();

        $this->dispatch('catalog/product_compare/add/product/1?nocookie=1');

        /** @var $session Mage_Catalog_Model_Session */
        $session = Mage::getSingleton('Mage_Catalog_Model_Session');
        $this->assertInstanceOf('Mage_Core_Model_Message_Success', $session->getMessages()->getLastAddedMessage());
        $this->assertContains('Simple Product 1 Name', $session->getMessages()->getLastAddedMessage()->getText());

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

        /** @var $session Mage_Catalog_Model_Session */
        $session = Mage::getSingleton('Mage_Catalog_Model_Session');
        $this->assertInstanceOf('Mage_Core_Model_Message_Success', $session->getMessages()->getLastAddedMessage());
        $this->assertContains('Simple Product 2 Name', $session->getMessages()->getLastAddedMessage()->getText());

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

        /** @var $session Mage_Catalog_Model_Session */
        $session = Mage::getSingleton('Mage_Catalog_Model_Session');
        $this->assertInstanceOf('Mage_Core_Model_Message_Success', $session->getMessages()->getLastAddedMessage());

        $this->assertRedirect();

        $this->_assertCompareListEquals(array());
    }
}
