<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for presence of the design editor toolbar on frontend pages
 *
 * @magentoDataFixture Mage/DesignEditor/_files/design_editor_active.php
 * @magentoDataFixture Mage/Catalog/controllers/_files/products.php
 */
class Mage_DesignEditor_Block_ToolbarCrosscuttingTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * Assert that a page content contains the design editor toolbar
     *
     * @param string $content
     */
    protected function _assertContainsToolbar($content)
    {
        $this->assertContains('id="vde_toolbar"', $content);
    }

    public function testCmsHomePage()
    {
        $this->markTestIncomplete('MAGETWO-1587');
        $this->dispatch('cms/index/index');
        $this->_assertContainsToolbar($this->getResponse()->getBody());
    }

    public function testCustomerAccountLogin()
    {
        $this->markTestIncomplete('MAGETWO-1587');
        $this->dispatch('customer/account/login');
        $this->_assertContainsToolbar($this->getResponse()->getBody());
    }

    public function testCatalogProductView()
    {
        $this->markTestIncomplete('MAGETWO-1587');
        $this->dispatch('catalog/product/view/id/1');
        $this->_assertContainsToolbar($this->getResponse()->getBody());
    }

    public function testCheckoutCart()
    {
        $this->markTestIncomplete('MAGETWO-1587');
        $this->dispatch('checkout/cart/index');
        $this->_assertContainsToolbar($this->getResponse()->getBody());
    }
}
