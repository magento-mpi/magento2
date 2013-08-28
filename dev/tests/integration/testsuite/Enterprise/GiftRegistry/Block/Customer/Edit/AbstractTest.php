<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_GiftRegistry
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Enterprise_GiftRegistry_Block_Customer_Edit_Abstract
 */
class Enterprise_GiftRegistry_Block_Customer_Edit_AbstractTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * Stub class name
     */
    const STUB_CLASS = 'Enterprise_GiftRegistry_Block_Customer_Edit_Abstract_Stub';

    public function testGetCalendarDateHtml()
    {
        $this->getMockForAbstractClass(
            'Enterprise_GiftRegistry_Block_Customer_Edit_Abstract', array(), self::STUB_CLASS, false
        );
        $block = Mage::app()->getLayout()->createBlock(self::STUB_CLASS);

        $value = null;
        $formatType = Magento_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM;

        $html = $block->getCalendarDateHtml('date_name', 'date_id', $value, $formatType);

        $dateFormat = Mage::app()->getLocale()->getDateFormat($formatType);
        $value = $block->formatDate($value, $formatType);

        $this->assertContains('dateFormat: "' . $dateFormat . '",', $html);
        $this->assertContains('value="' . $value . '"', $html);
    }
}
