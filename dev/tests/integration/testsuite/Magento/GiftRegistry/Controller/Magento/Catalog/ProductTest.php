<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftRegistry_Controller_Magento_Catalog_ProductTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testViewAction()
    {
        $this->getRequest()->setParam('options', Magento_GiftRegistry_Block_Product_View::FLAG);
        $this->dispatch('catalog/product/view/id/1');
        $body = $this->getResponse()->getBody();
        $this->assertContains('<span>Add to Gift Registry</span>', $body);
        $this->assertContains('http://localhost/index.php/giftregistry/index/cart/', $body);
    }
}
