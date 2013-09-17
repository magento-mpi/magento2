<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Catalog_Controller_Product (downloadable product type)
 */
class Magento_Downloadable_Controller_ProductTest extends Magento_TestFramework_TestCase_ControllerAbstract
{
    /**
     * @magentoDataFixture Magento/Downloadable/_files/product.php
     */
    public function testViewAction()
    {
        $this->dispatch('catalog/product/view/id/1');
        $this->assertContains(
            'catalog_product_view_type_downloadable',
            Mage::app()->getLayout()->getUpdate()->getHandles()
        );
        $responseBody = $this->getResponse()->getBody();
        $this->assertContains('Downloadable Product', $responseBody);
        $this->assertContains('In stock', $responseBody);
        $this->assertContains('Add to Cart', $responseBody);
        $actualLinkCount = substr_count($responseBody, 'Downloadable Product Link');
        $this->assertEquals(1, $actualLinkCount, 'Downloadable product link should appear on the page exactly once.');
    }
}
