<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Controller;

class ProductTest extends \Magento\TestFramework\TestCase\ControllerAbstract
{
    /**
     * @magentoDataFixture Magento/Catalog/_files/products.php
     * @dataProvider listActionDesignDataProvider
     */
    public function testListActionDesign($productId, $expectedDesign)
    {
        $this->getRequest()->setParam('id', $productId);
        $this->dispatch('review/product/list');
        $result = $this->getResponse()->getBody();
        $this->assertContains("static/frontend/{$expectedDesign}/en_US/Magento_Page/favicon.ico", $result);
    }

    /**
     * @return array
     */
    public function listActionDesignDataProvider()
    {
        return array(
            'custom product design' => array(2, 'magento_blank'),
        );
    }
}
