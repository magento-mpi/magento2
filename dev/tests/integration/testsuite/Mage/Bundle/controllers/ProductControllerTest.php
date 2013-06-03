<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Bundle
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Catalog_ProductController (bundle product type)
 */
class Mage_Bundle_ProductControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @magentoDataFixture Mage/Bundle/_files/product.php
     */
    public function testViewAction()
    {
        $this->dispatch('catalog/product/view/id/3');
        $this->assertContains(
            'catalog_product_view_type_bundle',
            Mage::app()->getLayout()->getUpdate()->getHandles()
        );
        $responseBody = $this->getResponse()->getBody();
        $this->assertContains('Bundle Product', $responseBody);
        $this->assertContains('In stock', $responseBody);
        $addToCartCount = substr_count($responseBody, '<span>Add to Cart</span>');
        $this->assertEquals(1, $addToCartCount, '"Add to Cart" button should appear on the page exactly once.');
        $actualLinkCount = substr_count($responseBody, '>Bundle Product Items<');
        $this->assertEquals(1, $actualLinkCount, 'Bundle product options should appear on the page exactly once.');
        $this->assertNotContains('class="options-container-big"', $responseBody);
        $this->assertSelectCount('#product-options-wrapper', 1, $responseBody);
    }
}
