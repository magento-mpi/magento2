<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Wishlist_Controller_IndexTest extends Magento_TestFramework_TestCase_ControllerAbstract
{
    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    protected function setUp()
    {
        parent::setUp();
        $logger = $this->getMock('Magento_Core_Model_Logger', array(), array(), '', false);
        $this->_customerSession = Mage::getModel('Magento_Customer_Model_Session', array($logger));
        $this->_customerSession->login('customer@example.com', 'password');
    }

    protected function tearDown()
    {
        $this->_customerSession->logout();
        $this->_customerSession = null;
        parent::tearDown();
    }

    /**
     * Verify wishlist view action
     *
     * The following is verified:
     * - Magento_Wishlist_Model_Resource_Item_Collection
     * - Magento_Wishlist_Block_Customer_Wishlist
     * - Magento_Wishlist_Block_Customer_Wishlist_Items
     * - Magento_Wishlist_Block_Customer_Wishlist_Item_Column
     * - Magento_Wishlist_Block_Customer_Wishlist_Item_Column_Cart
     * - Magento_Wishlist_Block_Customer_Wishlist_Item_Column_Comment
     * - Magento_Wishlist_Block_Customer_Wishlist_Button
     * - that Magento_Wishlist_Block_Customer_Wishlist_Item_Options doesn't throw a fatal error
     *
     * @magentoDataFixture Magento/Wishlist/_files/wishlist.php
     */
    public function testItemColumnBlock()
    {
        $this->dispatch('wishlist/index/index');
        $body = $this->getResponse()->getBody();
        $this->assertSelectCount('img[src~="small_image.jpg"][alt="Simple Product"]', 1, $body);
        $this->assertSelectCount('textarea[name~="description"]', 1, $body);
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple_xss.php
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testAddActionProductNameXss()
    {
        $this->dispatch('wishlist/index/add/product/1?nocookie=1');
        $messages = $this->_customerSession->getMessages()->getItems();
        $isProductNamePresent = false;
        foreach ($messages as $message) {
            if (strpos($message->getCode(), '&lt;script&gt;alert(&quot;xss&quot;);&lt;/script&gt;') !== false) {
                $isProductNamePresent = true;
            }
            $this->assertNotContains('<script>alert("xss");</script>', (string)$message->getCode());
        }
        $this->assertTrue($isProductNamePresent, 'Product name was not found in session messages');
    }
}
