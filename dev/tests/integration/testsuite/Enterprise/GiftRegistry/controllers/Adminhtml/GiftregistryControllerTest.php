<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_GiftRegistry
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftRegistry_Adminhtml_GiftregistryControllerTest extends Mage_Adminhtml_Utility_Controller
{
    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveAction()
    {
        $this->getRequest()->setPost('type', array(
            'code'       => 'test_registry',
            'label'      => 'Test',
            'sort_order' => 10,
            'is_listed'  => 1,
        ));
        $this->dispatch('backend/admin/giftregistry/save/store/0');
        /** @var $type Enterprise_GiftRegistry_Model_Type */
        $type = Mage::getModel('Enterprise_GiftRegistry_Model_Type');
        $type->setStoreId(0);

        $type = $type->load('test_registry', 'code');

        $this->assertInstanceOf('Enterprise_GiftRegistry_Model_Type', $type);
        $this->assertNotEmpty($type->getId());
    }
}
