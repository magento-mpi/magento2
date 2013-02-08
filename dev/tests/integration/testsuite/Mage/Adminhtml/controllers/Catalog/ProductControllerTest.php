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

class Mage_Adminhtml_Catalog_ProductControllerTest extends Mage_Backend_Utility_Controller
{
    /**
     * @magentoDataFixture Mage/Catalog/_files/product_configurable.php
     */
    public function testSaveActionAssociatedProductIds()
    {
        $associatedProductIds = array(3, 14, 15, 92);
        $this->getRequest()->setPost(array(
            'attributes' => array($this->_getConfigurableAttribute()->getId()),
            'associated_product_ids' => $associatedProductIds,
        ));

        $this->dispatch('backend/admin/catalog_product/save');

        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::registry('current_product');
        $this->assertEquals($associatedProductIds, $product->getAssociatedProductIds());

        /** @see Mage_Backend_Utility_Controller::assertPostConditions() */
        $this->markTestIncomplete('Suppressing admin error messages validation until the bug MAGETWO-7044 is fixed.');
    }

    /**
     * Retrieve configurable attribute instance
     *
     * @return Mage_Catalog_Model_Entity_Attribute
     */
    protected function _getConfigurableAttribute()
    {
        return Mage::getModel('Mage_Catalog_Model_Entity_Attribute')->loadByCode(
            Mage::getSingleton('Mage_Eav_Model_Config')->getEntityType('catalog_product')->getId(),
            'test_configurable'
        );
    }

    public function testSaveActionWithDangerRequest()
    {
        $this->getRequest()->setPost(array(
            'product' => array(
                'entity_id' => 15
            ),
        ));
        $this->dispatch('backend/admin/catalog_product/save');
        $this->assertSessionMessages(
            $this->equalTo(array('Unable to save product')), Mage_Core_Model_Message::ERROR
        );
        $this->assertRedirect($this->stringContains('/backend/admin/catalog_product/edit'));
    }
}
