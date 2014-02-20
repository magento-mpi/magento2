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

use Magento\Customer\Controller\Adminhtml\Index;

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
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->register(Index::REGISTRY_CURRENT_CUSTOMER_ID, 1);
        /** @var $layout \Magento\View\LayoutInterface */
        $layout = $objectManager->create(
            'Magento\Core\Model\Layout',
            array('area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE)
        );
        $this->_block = $layout->createBlock('Magento\Customer\Block\Adminhtml\Edit\Tab\View\Accordion');
    }

    protected function tearDown()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->unregister(Index::REGISTRY_CURRENT_CUSTOMER_ID);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoConfigFixture customer/account_share/scope 1
     */
    public function testToHtmlEmptyWebsiteShare()
    {
        $html = $this->_block->toHtml();
        
        $this->assertContains('Wishlist - 0 item(s)', $html);
        $this->assertContains('Shopping Cart - 0 item(s)', $html);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Core/_files/second_third_store.php
     * @magentoConfigFixture current_store customer/account_share/scope 0
     */
    public function testToHtmlEmptyGlobalShare()
    {
        $html = $this->_block->toHtml();

        $this->assertContains('Wishlist - 0 item(s)', $html);
        $this->assertContains('Shopping Cart of Main Website - 0 item(s)', $html);
        $this->assertContains('Shopping Cart of Second Website - 0 item(s)', $html);
        $this->assertContains('Shopping Cart of Third Website - 0 item(s)', $html);
    }
}
