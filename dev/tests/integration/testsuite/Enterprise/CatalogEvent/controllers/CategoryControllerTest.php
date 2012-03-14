<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_CatalogEvent
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Enterprise_CatalogEvent
 */
class Enterprise_CatalogEvent_CategoryControllerTest extends Mage_Adminhtml_Utility_Controller
{
    /**
     * @covers Enterprise_CatalogEvent_Block_Adminhtml_Catalog_Category_Edit_Buttons::addButtons()
     * in scope of MAGETWO-774
     * @magentoDataFixture Mage/Catalog/_files/categories.php
     */
    public function testEditCategoryAction()
    {
        $this->dispatch('admin/catalog_category/edit/id/3');
        $this->assertContains('Add Event...', $this->getResponse()->getBody());
    }

    /**
     * @covers Enterprise_CatalogEvent_Block_Adminhtml_Catalog_Category_Edit_Buttons::addButtons()
     * in scope of MAGETWO-774
     * @magentoDataFixture Mage/Catalog/_files/categories.php
     */
    public function testEditCategoryActionEditEvent()
    {
        $this->_addEvent();
        $this->dispatch('admin/catalog_category/edit/id/3');
        $this->assertContains('Edit Event...', $this->getResponse()->getBody());
    }

    protected function _addEvent()
    {
        $event = new Enterprise_CatalogEvent_Model_Event;
        $event->setStoreId(0);
        $event->setCategoryId('3');
        $event->setStoreDateStart(date('Y-m-d H:i:s'))->setStoreDateEnd(date('Y-m-d H:i:s', time() + 3600));
        $event->save();
    }
}
