<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Controller\Catalog;

class ProductTest extends \Magento\TestFramework\TestCase\ControllerAbstract
{
    /**
     * Covers Magento/TargetRule/view/frontend/catalog/product/list/related.html
     * Checks if related products are displayed
     *
     * @magentoDataFixture Magento/Catalog/controllers/_files/products.php
     * @magentoDataFixture Magento/TargetRule/_files/related.php
     */
    public function testProductViewActionRelated()
    {
        $this->dispatch('catalog/product/view/id/1');
        $content = $this->getResponse()->getBody();
        $this->assertContains('<div class="box-collateral box-related">', $content);
        $this->assertContains('Simple Product 2 Name', $content);
    }

    /**
     * Covers Magento/TargetRule/view/frontend/catalog/product/list/upsell.html
     * Checks if up-sell products are displayed
     *
     * @magentoDataFixture Magento/Catalog/controllers/_files/products.php
     * @magentoDataFixture Magento/TargetRule/_files/upsell.php
     */
    public function testProductViewActionUpsell()
    {
        $this->markTestSkipped('Can not be reproduced by manual testing');
        $this->dispatch('catalog/product/view/id/1');
        $content = $this->getResponse()->getBody();
        $this->assertContains('<div class="box-collateral box-up-sell">', $content);
        $this->assertContains('Simple Product 2 Name', $content);
    }
}
