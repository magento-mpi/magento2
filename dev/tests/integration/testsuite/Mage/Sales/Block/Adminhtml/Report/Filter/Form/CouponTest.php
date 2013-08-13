<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for Mage_Index_Model_Lock_Storage
 */

/**
 * @magentoAppArea adminhtml
 */
class Mage_Sales_Block_Adminhtml_Report_Filter_Form_CouponTest extends PHPUnit_Framework_TestCase
{
    /**
     * Application object
     *
     * @var Mage_Core_Model_App
     */
    protected $_application;

    protected function setUp()
    {
        parent::setUp();
        $this->_application = Magento_Test_Helper_Bootstrap::getObjectManager()->get('Mage_Core_Model_App');
    }

    /**
     * @covers Mage_Sales_Block_Adminhtml_Report_Filter_Form_Coupon::_afterToHtml
     */
    public function testAfterToHtml()
    {
        /** @var $block Mage_Sales_Block_Adminhtml_Report_Filter_Form_Coupon */
        $block = $this->_application->getLayout()->createBlock('Mage_Sales_Block_Adminhtml_Report_Filter_Form_Coupon');
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
