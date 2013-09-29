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

namespace Magento\Adminhtml\Block\Customer\Edit\Tab\View;

/**
 * @magentoAppArea adminhtml
 */
class AccordionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Adminhtml\Block\Customer\Edit\Tab\View\Accordion
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        /** @var $customer \Magento\Customer\Model\Customer */
        $customer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Model\Customer');
        $customer->load(1);
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->register('current_customer', $customer);
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = $objectManager->create(
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
