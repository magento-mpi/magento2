<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Payment\Block\Catalog\Product\View\Profile
 */
class Magento_Payment_Block_Catalog_Product_View_ProfileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testGetDateHtml()
    {
        $product = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Product');
        $product->setIsRecurring('1');
        $product->setRecurringProfile(array('start_date_is_editable' => true));
        Mage::register('current_product', $product);
        $block = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento\Payment\Block\Catalog\Product\View\Profile');
        $block->setLayout(Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Layout'));

        $html = $block->getDateHtml();
        $this->assertNotEmpty($html);
        $dateFormat = Mage::app()->getLocale()->getDateFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT);
        $timeFormat = Mage::app()->getLocale()->getTimeFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT);
        $this->assertContains('dateFormat: "' . $dateFormat . '",', $html);
        $this->assertContains('timeFormat: "' . $timeFormat . '",', $html);
    }
}
