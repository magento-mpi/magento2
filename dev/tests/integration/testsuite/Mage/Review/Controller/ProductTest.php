<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Review
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Review_Controller_ProductTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @magentoDataFixture Mage/Catalog/_files/products.php
     * @dataProvider listActionDesignDataProvider
     */
    public function testListActionDesign($productId, $expectedDesign)
    {
        $this->getRequest()->setParam('id', $productId);
        $this->dispatch('review/product/list');
        $result = $this->getResponse()->getBody();
        $this->assertContains("static/frontend/{$expectedDesign}/en_US/Mage_Page/favicon.ico", $result);
    }

    /**
     * @return array
     */
    public function listActionDesignDataProvider()
    {
        return array(
            'custom product design' => array(2, 'default/blank'),
        );
    }
}
