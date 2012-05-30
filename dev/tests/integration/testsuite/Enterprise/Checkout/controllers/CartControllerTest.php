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
}
