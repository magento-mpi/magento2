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

/**
 * @group module:Mage_Review
 */
class Mage_Review_ProductControllerTest extends Magento_Test_TestCase_ControllerAbstract
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
        $this->assertContains("media/skin/frontend/{$expectedDesign}/en_US/Mage_Page/favicon.ico", $result);
    }

    /**
     * @return array
     */
    public function listActionDesignDataProvider()
    {
        return array(
            'custom product design' => array(2, 'default/default/blue'),
        );
    }
}
