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
namespace Magento\Payment\Block\Catalog\Product\View;

class ProfileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testGetDateHtml()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $product = $objectManager->create('Magento\Catalog\Model\Product');
        $product->setIsRecurring('1');
        $product->setRecurringProfile(array('start_date_is_editable' => true));
        $objectManager->get('Magento\Core\Model\Registry')->register('current_product', $product);
        $block = $objectManager->create('Magento\Payment\Block\Catalog\Product\View\Profile');
        $block->setLayout($objectManager->create('Magento\Core\Model\Layout'));

        $html = $block->getDateHtml();
        $this->assertNotEmpty($html);
        $dateFormat = \Mage::app()->getLocale()->getDateFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT);
        $timeFormat = \Mage::app()->getLocale()->getTimeFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT);
        $this->assertContains('dateFormat: "' . $dateFormat . '",', $html);
        $this->assertContains('timeFormat: "' . $timeFormat . '",', $html);
    }
}
