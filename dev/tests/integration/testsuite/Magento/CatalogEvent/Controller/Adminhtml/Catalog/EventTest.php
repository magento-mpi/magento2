<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CatalogEvent\Controller\Adminhtml\Catalog;

/**
 * @magentoAppArea adminhtml
 */
class EventTest extends \Magento\Backend\Utility\Controller
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
     * @magentoConfigFixture current_store catalog/frontend/flat_catalog_product 1
     */
    public function testEditActionMultipleStore()
    {
        /** @var $event \Magento\CatalogEvent\Model\Event */
        $event = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\CatalogEvent\Model\Event'
        );
        $event->load(\Magento\CatalogEvent\Model\Event::DISPLAY_CATEGORY_PAGE, 'display_state');
        $this->dispatch('backend/admin/catalog_event/edit/id/' . $event->getId());
        $body = $this->getResponse()->getBody();

        $this->assertContains('name="store_switcher"', $body);
        $event->delete();
        unset($event);
    }
}
