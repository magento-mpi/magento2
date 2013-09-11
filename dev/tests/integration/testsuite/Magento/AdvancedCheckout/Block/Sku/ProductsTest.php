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

class Magento_AdvancedCheckout_Block_Sku_ProductsTest extends PHPUnit_Framework_TestCase
{
    public function testToHtml()
    {
        $block = Mage::app()->getLayout()->createBlock('\Magento\AdvancedCheckout\Block\Sku\Products')
            ->setTemplate('cart/sku/failed.phtml');
        $this->assertEmpty($block->toHtml());

        $item = array(
            'sku' => 'test',
            'code' => \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_SKU,
        );
        Mage::helper('Magento\AdvancedCheckout\Helper\Data')->getSession()
            ->setAffectedItems(array(Mage::app()->getStore()->getId() => array($item)));
        $this->assertContains('<form', $block->toHtml());
    }
}
