<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_ImportExport_Block_Adminhtml_Export_Filter.
 *
 * @group module:Mage_ImportExport
 */
class Mage_ImportExport_Block_Adminhtml_Export_FilterTest
    extends PHPUnit_Framework_TestCase
{
    public function testGetDateFromToHtmlWithValue()
    {
        $block = new Mage_ImportExport_Block_Adminhtml_Export_Filter;
        $method = new ReflectionMethod(
                    'Mage_ImportExport_Block_Adminhtml_Export_Filter', '_getDateFromToHtmlWithValue');
        $method->setAccessible(true);

        $attribute = new Mage_Eav_Model_Entity_Attribute(
           array(
               'attribute_code' => 'date',
               'backend_type' => 'datetime',
               'frontend_input' => 'date',
               'frontend_label' => 'Date',
            )
        );
        $html = $method->invoke($block, $attribute, null);
        $this->assertNotEmpty($html);

        $dateFormat = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $pieces = array_filter(explode('<strong>', $html));
        foreach ($pieces as $piece) {
            $this->assertContains('dateFormat: "' . $dateFormat . '",', $piece);
        }
    }
}
