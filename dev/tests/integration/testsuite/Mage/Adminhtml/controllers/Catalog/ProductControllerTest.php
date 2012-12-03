<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Adminhtml_Catalog_ProductControllerTest extends Mage_Adminhtml_Utility_Controller
{
    public function testSaveActionWithDangerRequest()
    {
        $this->getRequest()->setPost(array(
            'product' => array(
                'entity_id' => 15
            ),
        ));
        $this->dispatch('backend/admin/catalog_product/save');
        /** @var Mage_Backend_Model_Session $session */
        $session = Mage::getSingleton('Mage_Backend_Model_Session');
        $errorMessages = $session->getMessages()->getErrors();
        $this->assertCount(1, $errorMessages);
        $this->assertEquals('Unable to save product', $errorMessages[0]->getCode());
        $this->assertRedirect($this->stringContains('/backend/admin/catalog_product/edit'));
    }

    /**
     * @magentoDataFixture Mage/Catalog/_files/product_configurable.php
     */
    public function testQuickCreateActionWithDangerRequest()
    {
        $this->getRequest()->setPost(array(
            'simple_product' => array(
                'entity_id' => 15
            ),
            'product' => 1
        ));
        $this->dispatch('backend/admin/catalog_product/quickcreate');
        $this->assertContains('"error":{"message":"Unable to create product","fields":{"sku":null}}',
            $this->getResponse()->getBody());
    }
}
