<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Customer_Block_Account_LinkTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Customer_Block_Account_Link
     */
    protected $_block;

    /**
     * @var Magento_Page_Block_Template_Links
     */
    protected $_links;

    public function setUp()
    {
        $this->_block = Mage::app()->getLayout()->createBlock('Magento_Customer_Block_Account_Link');
        /** @var $layout Magento_Core_Model_Layout */
        $layout = Mage::getSingleton('Magento_Core_Model_Layout');
        $this->_block->setLayout($layout);
        $layout->addBlock('Magento_Page_Block_Template_Links', 'links');
        $this->_links = $layout->getBlock('links');
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testAddAccountLink()
    {
        $this->assertEmpty($this->_links->getLinks());
        $this->_block->addAccountLink('links', 1);

        $links = $this->_links->getLinks();
        $this->assertNotEmpty($links);
        $this->assertEquals('My Account', $links[1]->getLabel());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testAddRegisterLink()
    {
        $this->assertEmpty($this->_links->getLinks());
        $this->_block->addRegisterLink('links', 1);
        $links = $this->_links->getLinks();
        $this->assertEquals('register', $links[1]->getLabel());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testAddAuthLinkLogIn()
    {
        $this->assertEmpty($this->_links->getLinks());
        $this->_block->addAuthLink('links', 1);

        $links = $this->_links->getLinks();
        $this->assertEquals('Log In', $links[1]->getLabel());

    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testAddAuthLinkLogOut()
    {
        Mage::getSingleton('Magento_Customer_Model_Session')->login('customer@example.com', 'password');
        $this->_block->addAuthLink('links', 1);
        $links = $this->_links->getLinks();
        $this->assertEquals('Log Out', $links[1]->getLabel());
    }
}
