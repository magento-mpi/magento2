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

namespace Magento\Customer\Block\Account;

class LinkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Block\Account\Link
     */
    protected $_block;

    /**
     * @var \Magento\Page\Block\Template\Links
     */
    protected $_links;

    public function setUp()
    {
        $this->_block = \Mage::app()->getLayout()->createBlock('Magento\Customer\Block\Account\Link');
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Mage::getSingleton('Magento\Core\Model\Layout');
        $this->_block->setLayout($layout);
        $layout->addBlock('Magento\Page\Block\Template\Links', 'links');
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
        \Mage::getSingleton('Magento\Customer\Model\Session')->login('customer@example.com', 'password');
        $this->_block->addAuthLink('links', 1);
        $links = $this->_links->getLinks();
        $this->assertEquals('Log Out', $links[1]->getLabel());
    }
}
