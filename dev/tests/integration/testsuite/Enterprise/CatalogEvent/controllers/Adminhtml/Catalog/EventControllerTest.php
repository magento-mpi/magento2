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

class Enterprise_CatalogEvent_Adminhtml_Catalog_EventControllerTest extends Mage_Adminhtml_Utility_Controller
{
    public function testEditActionSingleStore()
    {
        $this->markTestIncomplete('Need to fix DI dependencies + controller');

        $this->dispatch('backend/admin/catalog_event/new');
        $body = $this->getResponse()->getBody();
        $this->assertNotContains(
            '<select name="store_switcher" id="store_switcher" onchange="return switchStore(this);',
            $body
        );
    }

    /**
     * magentoDataFixture Mage/Core/_files/store.php
     * magentoDataFixture Enterprise/CatalogEvent/_files/events.php
     */
    public function testEditActionMultipleStore()
    {
        $this->markTestIncomplete('Need to fix DI dependencies + controller + fixtures');

        $event = Mage::getModel('Enterprise_CatalogEvent_Model_Event');
        $event->load(Enterprise_CatalogEvent_Model_Event::DISPLAY_CATEGORY_PAGE, 'display_state');
        $this->dispatch('backend/admin/catalog_event/edit/id/' . $event->getId());
        $body = $this->getResponse()->getBody();
        $this->assertContains(
            '<select name="store_switcher" id="store_switcher" onchange="return switchStore(this);',
            $body
        );

        $event->delete();
        unset($event);
    }
}
