<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Wishlist
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Wishlist_IndexControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @var Mage_Customer_Model_Session
     */
    protected $_customerSession;

    protected function setUp()
    {
        parent::setUp();
        $this->_customerSession = Mage::getModel('Mage_Customer_Model_Session');
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
     * - Mage_Wishlist_Model_Resource_Item_Collection
     * - Mage_Wishlist_Block_Customer_Wishlist
     * - Mage_Wishlist_Block_Customer_Wishlist_Items
     * - Mage_Wishlist_Block_Customer_Wishlist_Item_Column
     * - Mage_Wishlist_Block_Customer_Wishlist_Item_Column_Cart
     * - Mage_Wishlist_Block_Customer_Wishlist_Item_Column_Comment
     * - Mage_Wishlist_Block_Customer_Wishlist_Button
     * - that Mage_Wishlist_Block_Customer_Wishlist_Item_Options doesn't throw a fatal error
     *
     * @magentoDataFixture Mage/Wishlist/_files/wishlist.php
     */
    public function testItemColumnBlock()
    {
        $this->dispatch('wishlist/index/index');
        $body = $this->getResponse()->getBody();
        $this->assertStringMatchesFormat('%A<img src="%Asmall_image.jpg" %A alt="Simple Product"%A/>%A', $body);
        $this->assertStringMatchesFormat('%A<textarea name="description[%d]"%A', $body);
    }

    /**
     * @magentoDataFixture Mage/Catalog/_files/product_simple_xss.php
     * @magentoDataFixture Mage/Customer/_files/customer.php
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
            $this->assertNotContains('<script>alert("xss");</script>', $message->getCode());
        }
        $this->assertTrue($isProductNamePresent, 'Product name was not found in session messages');
    }
}
