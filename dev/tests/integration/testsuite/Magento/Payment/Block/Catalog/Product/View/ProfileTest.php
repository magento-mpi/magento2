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
 * Test class for Magento_Payment_Block_Catalog_Product_View_Profile
 */
class Magento_Payment_Block_Catalog_Product_View_ProfileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testGetDateHtml()
    {
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        
        $product = $objectManager->create('Magento_Catalog_Model_Product');
        $product->setIsRecurring('1');
        $product->setRecurringProfile(array('start_date_is_editable' => true));
        $objectManager->get('Magento_Core_Model_Registry')->register('current_product', $product);
        $block = $objectManager->create('Magento_Payment_Block_Catalog_Product_View_Profile');
        $block->setLayout($objectManager->create('Magento_Core_Model_Layout'));

        $html = $block->getDateHtml();
        $this->assertNotEmpty($html);
        $dateFormat = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_LocaleInterface')
            ->getDateFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT);
        $timeFormat = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_LocaleInterface')
            ->getTimeFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT);
        $this->assertContains('dateFormat: "' . $dateFormat . '",', $html);
        $this->assertContains('timeFormat: "' . $timeFormat . '",', $html);
    }
}
