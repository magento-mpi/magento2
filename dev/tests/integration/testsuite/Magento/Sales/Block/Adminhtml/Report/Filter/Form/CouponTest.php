<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for \Magento\Index\Model\Lock\Storage
 */
namespace Magento\Sales\Block\Adminhtml\Report\Filter\Form;

/**
 * @magentoAppArea adminhtml
 */
class CouponTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Application object
     *
     * @var \Magento\Core\Model\App
     */
    protected $_application;

    protected function setUp()
    {
        parent::setUp();
        $this->_application = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Core\Model\App'
        );
    }

    /**
     * @covers \Magento\Sales\Block\Adminhtml\Report\Filter\Form\Coupon::_afterToHtml
     */
    public function testAfterToHtml()
    {
        /** @var $block \Magento\Sales\Block\Adminhtml\Report\Filter\Form\Coupon */
        $block = $this->_application->getLayout()->createBlock(
            'Magento\Sales\Block\Adminhtml\Report\Filter\Form\Coupon'
        );
        $block->setFilterData(new \Magento\Object());
        $html = $block->toHtml();

        $expectedStrings = array(
            'FormElementDependenceController',
            'sales_report_rules_list',
            'sales_report_price_rule_type'
        );
        foreach ($expectedStrings as $expectedString) {
            $this->assertContains($expectedString, $html);
        }
    }
}
