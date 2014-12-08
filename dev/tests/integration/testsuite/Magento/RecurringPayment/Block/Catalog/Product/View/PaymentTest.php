<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\RecurringPayment\Block\Catalog\Product\View\Payment
 */
namespace Magento\RecurringPayment\Block\Catalog\Product\View;

class PaymentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testGetDateHtml()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Framework\App\State')->setAreaCode('frontend');

        $product = $objectManager->create('Magento\Catalog\Model\Product');
        $product->setIsRecurring('1');
        $product->setRecurringPayment(['start_date_is_editable' => true]);
        $objectManager->get('Magento\Framework\Registry')->register('current_product', $product);
        $block = $objectManager->create('Magento\RecurringPayment\Block\Catalog\Product\View\Payment');
        $block->setLayout($objectManager->create('Magento\Framework\View\Layout'));

        $html = $block->getDateHtml();
        $this->assertNotEmpty($html);
        $dateFormat = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\Stdlib\DateTime\TimezoneInterface'
        )->getDateFormat(
            \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_SHORT
        );
        $timeFormat = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\Stdlib\DateTime\TimezoneInterface'
        )->getTimeFormat(
            \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_SHORT
        );
        $this->assertContains('dateFormat: "' . $dateFormat . '",', $html);
        $this->assertContains('timeFormat: "' . $timeFormat . '",', $html);
    }
}
