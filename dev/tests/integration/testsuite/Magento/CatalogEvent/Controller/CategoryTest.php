<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogEvent\Controller;

/**
 * @magentoAppArea adminhtml
 */
class CategoryTest extends \Magento\Backend\Utility\Controller
{
    /**
     * Covers \Magento\CatalogEvent\Block\Adminhtml\Catalog\Category\Edit\Buttons::addButtons for Add Event button
     *
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     */
    public function testEditCategoryAction()
    {
        $this->dispatch('backend/catalog/category/edit/id/3');
        $this->assertContains(
            'onclick="setLocation(\'http://localhost/index.php/backend/admin/catalog_event/new/category_id/',
            $this->getResponse()->getBody()
        );
    }

    /**
     * Covers \Magento\CatalogEvent\Block\Adminhtml\Catalog\Category\Edit\Buttons::addButtons for Edit Event button
     *
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture eventDataFixture
     */
    public function testEditCategoryActionEditEvent()
    {
        $this->dispatch('backend/catalog/category/edit/id/3');
        $this->assertContains(
            'onclick="setLocation(\'http://localhost/index.php/backend/admin/catalog_event/edit/id/',
            $this->getResponse()->getBody()
        );
    }

    public static function eventDataFixture()
    {
        /** @var $event \Magento\CatalogEvent\Model\Event */
        $event = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\CatalogEvent\Model\Event'
        );
        $event->setStoreId(0);
        $event->setCategoryId('3');
        $event->setStoreDateStart(date('Y-m-d H:i:s'))->setStoreDateEnd(date('Y-m-d H:i:s', time() + 3600));
        $event->save();
    }
}
