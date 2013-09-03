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
 * Test for Magento_Index_Model_Lock_Storage
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Sales_Block_Adminhtml_Report_Filter_Form_CouponTest extends PHPUnit_Framework_TestCase
{
    /**
     * Application object
     *
     * @var Magento_Core_Model_App
     */
    protected $_application;

    protected function setUp()
    {
        parent::setUp();
        $this->_application = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_App');
    }

    /**
     * @covers Magento_Sales_Block_Adminhtml_Report_Filter_Form_Coupon::_afterToHtml
     */
    public function testAfterToHtml()
    {
        /** @var $block Magento_Sales_Block_Adminhtml_Report_Filter_Form_Coupon */
        $block = $this->_application->getLayout()
            ->createBlock('Magento_Sales_Block_Adminhtml_Report_Filter_Form_Coupon');
        $block->setFilterData(new Magento_Object());
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
