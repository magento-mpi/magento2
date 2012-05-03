<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Checkout_Block_Sku_ProductsTest extends PHPUnit_Framework_TestCase
{
    public function testToHtml()
    {
        $block = Mage::app()->getLayout()->createBlock('Enterprise_Checkout_Block_Sku_Products')
            ->setTemplate('cart/sku/failed.phtml');
        $this->assertEmpty($block->toHtml());

        $item = array(
            'sku' => 'test',
            'code' => Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_SKU,
        );
        Mage::helper('Enterprise_Checkout_Helper_Data')->getSession()
            ->setAffectedItems(array(Mage::app()->getStore()->getId() => array($item)));
        $this->assertContains('<form', $block->toHtml());
    }
}
