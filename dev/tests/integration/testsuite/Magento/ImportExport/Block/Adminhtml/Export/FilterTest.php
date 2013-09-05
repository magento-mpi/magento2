<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_ImportExport_Block_Adminhtml_Export_Filter
 */
class Magento_ImportExport_Block_Adminhtml_Export_FilterTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testGetDateFromToHtmlWithValue()
    {
        Mage::getDesign()->setArea(Magento_Core_Model_App_Area::AREA_ADMINHTML)->setDefaultDesignTheme();
        $block = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_ImportExport_Block_Adminhtml_Export_Filter');
        $method = new ReflectionMethod(
                    'Magento_ImportExport_Block_Adminhtml_Export_Filter', '_getDateFromToHtmlWithValue');
        $method->setAccessible(true);

        $arguments = array(
            'data' => array(
                'attribute_code' => 'date',
                'backend_type'   => 'datetime',
                'frontend_input' => 'date',
                'frontend_label' => 'Date',
            )
        );
        $attribute = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Eav_Model_Entity_Attribute', $arguments);
        $html = $method->invoke($block, $attribute, null);
        $this->assertNotEmpty($html);

        $dateFormat = Mage::app()->getLocale()->getDateFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT);
        $pieces = array_filter(explode('<strong>', $html));
        foreach ($pieces as $piece) {
            $this->assertContains('dateFormat: "' . $dateFormat . '",', $piece);
        }
    }
}
