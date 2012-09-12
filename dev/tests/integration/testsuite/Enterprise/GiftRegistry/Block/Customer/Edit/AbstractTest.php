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
 * Test class for Enterprise_GiftRegistry_Block_Customer_Edit_Abstract.
 *
 * @group module:Enterprise_GiftRegistry
 */
class Enterprise_GiftRegistry_Block_Customer_Edit_AbstractTest
    extends PHPUnit_Framework_TestCase
{
    public function testGetCalendarDateHtml()
    {
        $block = $this->getMockForAbstractClass('Enterprise_GiftRegistry_Block_Customer_Edit_Abstract')
            ->setLayout(new Mage_Core_Model_Layout);

        $value = null;
        $formatType = Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM;

        $html = $block->getCalendarDateHtml('date_name', 'date_id', $value, $formatType);

        $dateFormat = Mage::app()->getLocale()->getDateFormat($formatType);
        $value = $block->formatDate($value, $formatType);

        $this->assertContains('dateFormat: "' . $dateFormat . '",', $html);
        $this->assertContains('value="' . $value . '"', $html);
    }
}
