<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Wishlist\Block\Share;

class WishlistTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Wishlist\Block\Share\Wishlist
     */
    protected $_block;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    public function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');
        $this->_block = $this->_objectManager->create('Magento\Wishlist\Block\Share\Wishlist');
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Wishlist/_files/wishlist.php
     */
    public function testGetWishlistCustomer()
    {
        $this->_customerSession->loginById(1);
        $this->assertEquals($this->_customerSession->getCustomerDataObject(), $this->_block->getWishlistCustomer());

    }
}
 