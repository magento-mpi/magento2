<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Catalog_ProductController.
 */
class Mage_Checkout_CartControllerTest extends Magento_Test_TestCase_ControllerAbstract
{

    /**
     * Test for Mage_Catalog_ProductController::configureAction()
     */
    public function testConfigureAction()
    {
        $testPlan = array(
            'bundle_product' => array(
                'fixture' => 'Mage/Checkout/_files/product_bundle.php',
                'must_have' => array('<div id="bundle-product-wrapper">')
            ),
            'simple_product' => array(
                'fixture' => 'Mage/Checkout/_files/product.php',
                'must_have' => array('<button type="button" title="Update Cart" class="button btn-cart"')
            ),
            'simple_with_custom_option' => array(
                'fixture' => 'Mage/Checkout/_files/product_with_custom_option.php',
                'must_have' => array('<button type="button" title="Update Cart" class="button btn-cart"')
            ),
            'downloadable_product' => array(
                'fixture' => 'Mage/Checkout/_files/product_downloadable.php',
                'must_have' => array(
                    '<ul id="downloadable-links-list" class="options-list">',
                    '<button type="button" title="Update Cart" class="button btn-cart"')
            ),
            'configurable_product' => array(
                'fixture' => 'Mage/Checkout/_files/product_configurable.php',
                'must_have' => array(
                    '<select name="super_attribute',
                    '<button type="button" title="Update Cart" class="button btn-cart"')
            ),
            'gift_card' => array(
                'fixture' => 'Mage/Checkout/_files/product_gift.php',
                'must_have' => array(
                    '<input type="text" id="giftcard_amount_input"',
                    '<button type="button" title="Update Cart" class="button btn-cart"')
            ),
        );
        $this->setUp();
        $adapter = Mage::getSingleton('Mage_Core_Model_Resource')->getConnection('write');
        foreach ($testPlan as $testCode => $testParams) {
            $adapter->beginTransaction();
            require __DIR__ . '/../../../' . $testParams['fixture'];
            $quoteItemId = Mage::registry('product/quoteItemId');
            $this->getResponse()->clearBody();
            $this->dispatch('checkout/cart/configure/id/' . $quoteItemId);
            $out = $this->getResponse()->getBody();
            foreach ($testParams['must_have'] as $haystack) {
                $this->assertContains($haystack, $out, 'Route checkout/cart/configure ' . $testCode);
            }
            $adapter->rollBack();
            Mage::unregister('product/quoteItemId');
            Mage::unregister('application_params');
            Mage::unregister('current_product');
            Mage::unregister('product');
            Mage::unregister('_singleton/Mage_Eav_Model_Config');
            Mage::unregister('_singleton/Mage_Catalog_Model_Product_Option');
            Mage::unregister('_singleton/Mage_Core_Model_Layout');
        }
    }
}
