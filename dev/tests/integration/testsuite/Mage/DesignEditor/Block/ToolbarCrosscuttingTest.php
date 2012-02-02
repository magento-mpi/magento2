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
 * @group module:Mage_DesignEditor
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
        $this->assertContains('design-editor-toolbar', $content);
    }

    /**
     * @group module:Mage_Cms
     */
    public function testCmsHomePage()
    {
        $this->dispatch('cms/index/index');
        $this->_assertContainsToolbar($this->getResponse()->getBody());
    }

    /**
     * @group module:Mage_Customer
     */
    public function testCustomerAccountLogin()
    {
        $this->dispatch('customer/account/login');
        $this->_assertContainsToolbar($this->getResponse()->getBody());
    }

    /**
     * @group module:Mage_Catalog
     */
    public function testCatalogProductView()
    {
        $this->dispatch('catalog/product/view/id/1');
        $this->_assertContainsToolbar($this->getResponse()->getBody());
    }

    /**
     * @group module:Mage_Checkout
     */
    public function testCheckoutCart()
    {
        $this->dispatch('checkout/cart/index');
        $this->_assertContainsToolbar($this->getResponse()->getBody());
    }
}
