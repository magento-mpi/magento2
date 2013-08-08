<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Payment
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Payment_Block_Catalog_Product_View_Profile
 */
class Mage_Payment_Block_Catalog_Product_View_ProfileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testGetDateHtml()
    {
        $product = Mage::getObjectManager()->create('Mage_Catalog_Model_Product');
        $product->setIsRecurring('1');
        $product->setRecurringProfile(array('start_date_is_editable' => true));
        Mage::register('current_product', $product);
        $block = Mage::getObjectManager()->create('Mage_Payment_Block_Catalog_Product_View_Profile');
        $block->setLayout(Mage::getObjectManager()->create('Magento_Core_Model_Layout'));

        $html = $block->getDateHtml();
        $this->assertNotEmpty($html);
        $dateFormat = Mage::app()->getLocale()->getDateFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT);
        $timeFormat = Mage::app()->getLocale()->getTimeFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT);
        $this->assertContains('dateFormat: "' . $dateFormat . '",', $html);
        $this->assertContains('timeFormat: "' . $timeFormat . '",', $html);
    }
}
