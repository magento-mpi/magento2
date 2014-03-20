<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Edit\Tab\View;

use Magento\Customer\Controller\RegistryConstants;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Class SalesTest
 *
 * @magentoAppArea adminhtml
 */
class SalesTest extends \PHPUnit_Framework_TestCase
{
    const MAIN_WEBSITE = 1;

    /**
     * Sales block under test.
     *
     * @var Sales
     */
    private $block;

    /**
     * Core registry.
     *
     * @var \Magento\Registry
     */
    private $coreRegistry;

    /**
     * Sales order view Html.
     *
     * @var string
     */
    private $html;

    /**
     * Execute per test initialization.
     */
    public function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get('Magento\App\State')->setAreaCode('adminhtml');

        $this->coreRegistry = $objectManager->get('Magento\Registry');
        $this->coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER_ID, 1);

        $this->block = $objectManager->get(
            'Magento\View\LayoutInterface'
        )->createBlock(
            'Magento\Customer\Block\Adminhtml\Edit\Tab\View\Sales',
            'sales_' . mt_rand(),
            array('coreRegistry' => $this->coreRegistry)
        )->setTemplate(
            'tab/view/sales.phtml'
        );
        $this->html = $this->block->toHtml();
    }

    /**
     * Execute post test cleanup.
     */
    public function tearDown()
    {
        $this->coreRegistry->unregister(RegistryConstants::CURRENT_CUSTOMER_ID);
        $this->html = '';
    }

    /**
     * Test basic currency formatting on the Main Website.
     */
    public function testFormatCurrency()
    {
        $this->assertEquals(
            '<span class="price">$10.00</span>',
            $this->block->formatCurrency(10.00, self::MAIN_WEBSITE)
        );
    }

    /**
     * Verify that the website is not in single store mode.
     */
    public function testIsSingleStoreMode()
    {
        $this->assertFalse($this->block->isSingleStoreMode());
    }

    /**
     * Verify sales totals. No sales so there are no totals.
     */
    public function testGetTotals()
    {
        $this->assertEquals(
            array('lifetime' => 0, 'base_lifetime' => 0, 'base_avgsale' => 0, 'num_orders' => 0),
            $this->block->getTotals()->getData()
        );
    }

    /**
     * Verify that there are no rows in the sales order grid.
     */
    public function testGetRows()
    {
        $this->assertEmpty($this->block->getRows());
    }

    /**
     * Verify that the Main Website has no websites.
     */
    public function testGetWebsiteCount()
    {
        $this->assertEquals(0, $this->block->getWebsiteCount(self::MAIN_WEBSITE));
    }

    /**
     * Verify basic content of the sales view Html.
     */
    public function testToHtml()
    {
        $this->assertContains('<span class="title">Sales Statistics</span>', $this->html);
        $this->assertContains('<strong>All Store Views</strong>', $this->html);
    }
}
