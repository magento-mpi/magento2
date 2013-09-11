<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Invitation_Block_LinkTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Invitation\Block\Link
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = Mage::app()->getLayout()->createBlock('\Magento\Invitation\Block\Link');
    }

    /**
     * @magentoConfigFixture current_store magento_invitation/general/enabled 1
     * @magentoConfigFixture current_store magento_invitation/general/enabled_on_front 1
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testAddAccountLink()
    {
        $layout = Mage::app()->getLayout();
        $this->_block->setLayout($layout);
        $layout->addBlock('\Magento\Page\Block\Template\Links', 'account.links');

        /* @var \Magento\Page\Block\Template\Links $links */
        $links = $layout->getBlock('account.links');
        $this->assertEmpty($links->getLinks());

        $this->_block->addAccountLink();
        $this->assertEmpty($links->getLinks());

        Mage::getSingleton('Magento\Customer\Model\Session')->login('customer@example.com', 'password');
        $this->_block->addAccountLink();
        $links = $links->getLinks();
        $this->assertNotEmpty($links);
        $this->assertEquals('Send Invitations', $links[1]->getLabel());
    }
}
