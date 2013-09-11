<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogEvent
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_CatalogEvent_Controller_Adminhtml_Catalog_EventTest extends Magento_Backend_Utility_Controller
{
    public function testEditActionSingleStore()
    {
        $this->dispatch('backend/admin/catalog_event/new');
        $body = $this->getResponse()->getBody();
        $this->assertNotContains('name="store_switcher"', $body);
    }

    /**
     * @magentoDataFixture Magento/Core/_files/store.php
     * @magentoDataFixture Magento/CatalogEvent/_files/events.php
     */
    public function testEditActionMultipleStore()
    {
        /** @var $event \Magento\CatalogEvent\Model\Event */
        $event = Mage::getModel('\Magento\CatalogEvent\Model\Event');
        $event->load(\Magento\CatalogEvent\Model\Event::DISPLAY_CATEGORY_PAGE, 'display_state');
        $this->dispatch('backend/admin/catalog_event/edit/id/' . $event->getId());
        $body = $this->getResponse()->getBody();

        $this->assertContains('name="store_switcher"', $body);
        $event->delete();
        unset($event);
    }
}
