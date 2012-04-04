<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_View_AccordionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Adminhtml_Block_Customer_Edit_Tab_View_Accordion
     */
    protected $_block;

    protected function setUp()
    {
        $customer = new Mage_Customer_Model_Customer;
        $customer->load(1);
        Mage::register('current_customer', $customer);
        $layout = new Mage_Core_Model_Layout(array('area' => 'adminhtml'));
        $this->_block = $layout->createBlock('Mage_Adminhtml_Block_Customer_Edit_Tab_View_Accordion');
    }

    /**
     * @magentoDataFixture Mage/Customer/_files/customer.php
     */
    public function testToHtml()
    {
        $this->assertContains('Wishlist - 0 item(s)', $this->_block->toHtml());
    }
}
