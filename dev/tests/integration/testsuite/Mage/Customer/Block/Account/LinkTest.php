<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Customer_Block_Account_LinkTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Customer_Block_Account_Link
     */
    protected $_block;

    /**
     * @var Mage_Page_Block_Template_Links
     */
    protected $_links;

    public function setUp()
    {
        $this->_block = new Mage_Customer_Block_Account_Link();
        $layout = new Mage_Core_Model_Layout;
        $this->_block->setLayout($layout);
        $layout->addBlock('Mage_Page_Block_Template_Links', 'links');
        $this->_links = $layout->getBlock('links');
    }

    protected function tearDown()
    {
        $this->_block = null;
        $this->_links = null;
    }

    public function testAddAccountLink()
    {
        $this->assertEmpty($this->_links->getLinks());
        $this->_block->addAccountLink('links', 1);

        $links = $this->_links->getLinks();
        $this->assertNotEmpty($links);
        $this->assertEquals('My Account', $links[1]->getLabel());
    }

    public function testAddRegisterLink()
    {
        $this->assertEmpty($this->_links->getLinks());
        $this->_block->addRegisterLink('links', 1);
        $links = $this->_links->getLinks();
        $this->assertEquals('register', $links[1]->getLabel());
    }

    public function testAddAuthLinkLogIn()
    {
        $this->assertEmpty($this->_links->getLinks());
        $this->_block->addAuthLink('links', 1);

        $links = $this->_links->getLinks();
        $this->assertEquals('Log In', $links[1]->getLabel());

    }

    /**
     * @magentoDataFixture Mage/Customer/_files/customer.php
     */
    public function testAddAuthLinkLogOut()
    {
        Mage::getSingleton('Mage_Customer_Model_Session')->login('customer@example.com', 'password');
        $this->_block->addAuthLink('links', 1);
        $links = $this->_links->getLinks();
        $this->assertEquals('Log Out', $links[1]->getLabel());
    }
}
