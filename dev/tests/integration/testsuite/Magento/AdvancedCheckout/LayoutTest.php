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

class Magento_AdvancedCheckout_LayoutTest extends PHPUnit_Framework_TestCase
{
    public function testCartLayout()
    {
        Mage::getDesign()->setDesignTheme('magento_fixed_width');
        $layout = Mage::getModel('Magento_Core_Model_Layout');
        $layout->getUpdate()->addHandle('checkout_cart_index');
        $layout->getUpdate()->load();
        $this->assertNotEmpty($layout->getUpdate()->asSimplexml()->xpath('//block[@name="sku.failed.products"]'));
    }
}
