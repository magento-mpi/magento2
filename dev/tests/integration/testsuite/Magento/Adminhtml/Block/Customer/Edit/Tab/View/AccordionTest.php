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
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Block_Customer_Edit_Tab_View_AccordionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Adminhtml\Block\Customer\Edit\Tab\View\Accordion
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        /** @var $customer \Magento\Customer\Model\Customer */
        $customer = Mage::getModel('Magento\Customer\Model\Customer');
        $customer->load(1);
        Mage::register('current_customer', $customer);
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = Mage::getModel(
            'Magento\Core\Model\Layout',
            array('area' => \Magento\Core\Model\App\Area::AREA_ADMINHTML)
        );
        $this->_block = $layout->createBlock('Magento\Adminhtml\Block\Customer\Edit\Tab\View\Accordion');
    }

    /**
     * magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testToHtml()
    {
        $this->assertContains('Wishlist - 0 item(s)', $this->_block->toHtml());
    }
}
