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
class Enterprise_CatalogEvent_Adminhtml_Catalog_EventControllerTest extends Mage_Adminhtml_Utility_Controller
{
    public function testEditActionSingleStore()
    {
        $this->dispatch('admin/catalog_event/new');
        $body = $this->getResponse()->getBody();
        $this->assertNotContains('Choose Store View', $body);
        $this->assertNotContains('Fatal error', $body);
    }

    /**
     * @magentoDataFixture Mage/Core/_files/store.php
     */
    public function testEditActionMultipleStore()
    {
        $event = new Enterprise_CatalogEvent_Model_Event;
        $event
            ->setCategoryId(null)
            ->setDateStart(date('Y-m-d H:i:s', strtotime('-1 year')))
            ->setDateEnd(date('Y-m-d H:i:s', strtotime('-1 month')))
            ->setDisplayState(Enterprise_CatalogEvent_Model_Event::DISPLAY_CATEGORY_PAGE)
            ->setSortOrder(30)
            ->save()
        ;
        $this->dispatch('admin/catalog_event/edit/id/' . $event->getId());
        $body = $this->getResponse()->getBody();
        $this->assertContains('Choose Store View', $body);

        $event->delete();
        unset($event);
    }
}
