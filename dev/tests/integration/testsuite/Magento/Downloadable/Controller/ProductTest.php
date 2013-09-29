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
 * Test class for \Magento\Catalog\Controller\Product (downloadable product type)
 */
namespace Magento\Downloadable\Controller;

class ProductTest extends \Magento\TestFramework\TestCase\ControllerAbstract
{
    /**
     * @magentoDataFixture Magento/Downloadable/_files/product.php
     */
    public function testViewAction()
    {
        $this->dispatch('catalog/product/view/id/1');
        $this->assertContains(
            'catalog_product_view_type_downloadable',
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Layout')
                ->getUpdate()->getHandles()
        );
        $responseBody = $this->getResponse()->getBody();
        $this->assertContains('Downloadable Product', $responseBody);
        $this->assertContains('In stock', $responseBody);
        $this->assertContains('Add to Cart', $responseBody);
        $actualLinkCount = substr_count($responseBody, 'Downloadable Product Link');
        $this->assertEquals(1, $actualLinkCount, 'Downloadable product link should appear on the page exactly once.');
    }
}
