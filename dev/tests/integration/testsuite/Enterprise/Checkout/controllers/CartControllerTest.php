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

require_once __DIR__ . '/../../../Mage/Checkout/controllers/CartControllerTest.php';
class Enterprise_Checkout_CartControllerTest extends Mage_Checkout_CartControllerTest
{
    protected $_products = array(
        'bundle_product' => array(
            'fixture' => 'Mage/Bundle/_files/product.php',
            'expected' => array('<button type="button" title="Update Cart" class="button btn-cart"')
        ),
        'simple_product' => array(
            'fixture' => 'Mage/Catalog/_files/product_simple.php',
            'expected' => array('<button type="button" title="Update Cart" class="button btn-cart"')
        ),
        'downloadable_product' => array(
            'fixture' => 'Mage/Downloadable/_files/product.php',
            'expected' => array(
                '<ul id="downloadable-links-list" class="options-list">',
                '<button type="button" title="Update Cart" class="button btn-cart"')
        ),
        'configurable_product' => array(
            'fixture' => 'Mage/Catalog/_files/product_configurable.php',
            'expected' => array(
                '<select name="super_attribute',
                '<button type="button" title="Update Cart" class="button btn-cart"')
        ),
        'gift_card' => array(
            'fixture' => 'Enterprise/GiftCard/_files/gift_card.php',
            'expected' => array(
                '<input type="text" id="giftcard_amount_input"',
                '<button type="button" title="Update Cart" class="button btn-cart"')
        )
    );

    /**
     * Test for Mage_Catalog_ProductController::configureAction()
     */
    public function testConfigureAction()
    {
        $this->_configureAction['gift_card'] = array(
            'fixture' => 'Enterprise/Checkout/_files/product_gift.php',
            'must_have' => array(
                '<input type="text" id="giftcard_amount_input"',
                '<button type="button" title="Update Cart" class="button btn-cart"')
        );
        parent::testConfigureAction();
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testConfigureFailedAction()
    {
        $this->setUp();
        $adapter = Mage::getSingleton('Mage_Core_Model_Resource')->getConnection('write');
        foreach ($this->_products as $testCode => $testParams) {
            $product = null;
            $adapter->beginTransaction();
            require __DIR__ . '/../../../' . $testParams['fixture'];
            $this->getResponse()->clearBody();
            $this->dispatch('checkout/cart/configureFailed/id/' . $product->getId());
            $out = $this->getResponse()->getBody();
            $adapter->rollBack();
            foreach ($testParams['expected'] as $expected) {
                $this->assertContains($expected, $out, 'Route checkout/cart/configureFailed ' . $testCode);
            }
        }
    }
}
