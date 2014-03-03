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

namespace Magento\Customer\Block\Adminhtml\Edit\Tab\View;

/**
 * @magentoAppArea adminhtml
 */
class AccordionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Block\Adminhtml\Edit\Tab\View\Accordion
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
        $objectManager->get('Magento\Registry')->register('current_customer', $customer);
        /** @var $layout \Magento\View\LayoutInterface */
        $layout = $objectManager->create(
            'Magento\Core\Model\Layout',
            array('area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE)
        );
        $this->_block = $layout->createBlock('Magento\Customer\Block\Adminhtml\Edit\Tab\View\Accordion');
    }

    /**
     * magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testToHtml()
    {
        $this->assertContains('tab_content_customerViewAccordion', $this->_block->toHtml());
    }
}
