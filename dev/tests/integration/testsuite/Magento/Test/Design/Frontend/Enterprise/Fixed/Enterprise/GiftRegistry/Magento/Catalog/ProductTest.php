<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Test_Design_Frontend_Enterprise_Fixed_Magento_GiftRegistry_Magento_Catalog_ProductTest
    extends Magento_TestFramework_TestCase_ControllerAbstract
{
    /**
     * @magentoConfigFixture frontend/design/theme/full_name magento_fixed_width
     * @magentoDataFixture Magento/Bundle/_files/product.php
     */
    public function testViewActionBundle()
    {
        $this->getRequest()->setParam('options', Magento_GiftRegistry_Block_Product_View::FLAG);
        $this->dispatch('catalog/product/view/id/3');
        $body = $this->getResponse()->getBody();
        $this->assertContains('<span>Customize and Add to Gift Registry</span>', $body);
        $this->assertContains('<span>Add to Gift Registry</span>', $body);
        $this->assertContains('http://localhost/index.php/giftregistry/index/cart/', $body);
    }
}
