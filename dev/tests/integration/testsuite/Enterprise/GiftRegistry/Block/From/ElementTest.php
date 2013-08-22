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
 * Test class for Enterprise_GiftRegistry_Block_Form_Element
 */
class Enterprise_GiftRegistry_Block_Form_ElementTest extends PHPUnit_Framework_TestCase
{
    public function testGetCalendarDateHtml()
    {
        $block = Mage::app()->getLayout()->createBlock('Enterprise_GiftRegistry_Block_Form_Element');

        $value = null;
        $formatType = Magento_Core_Model_LocaleInterface::FORMAT_TYPE_FULL;

        $html = $block->getCalendarDateHtml('date_name', 'date_id', $value, $formatType);

        $dateFormat = Mage::app()->getLocale()->getDateFormat($formatType);

        $this->assertContains('dateFormat: "' . $dateFormat . '",', $html);
        $this->assertContains('value=""', $html);
    }
}
