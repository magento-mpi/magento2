<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Block\Sku;

class ProductsTest extends \PHPUnit_Framework_TestCase
{
    public function testToHtml()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\State')->setAreaCode('frontend');
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\View\LayoutInterface'
        )->createBlock(
            'Magento\AdvancedCheckout\Block\Sku\Products'
        )->setTemplate(
            'cart/sku/failed.phtml'
        );
        $this->assertEmpty($block->toHtml());

        $item = array('sku' => 'test', 'code' => \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_SKU);
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\AdvancedCheckout\Helper\Data'
        )->getSession()->setAffectedItems(
            array(
                \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                    'Magento\Store\Model\StoreManagerInterface'
                )->getStore()->getId() => array(
                    $item
                )
            )
        );
        $this->assertContains('<form', $block->toHtml());
    }
}
