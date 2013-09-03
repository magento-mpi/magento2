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
class Magento_CatalogEvent_Controller_CategoryTest extends Magento_Backend_Utility_Controller
{
    /**
     * Covers Magento_CatalogEvent_Block_Adminhtml_Catalog_Category_Edit_Buttons::addButtons for Add Event button
     *
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     */
    public function testEditCategoryAction()
    {
        $this->dispatch('backend/admin/catalog_category/edit/id/3');
        $this->assertContains(
            'onclick="setLocation(\'http://localhost/index.php/backend/admin/catalog_event/new/category_id/',
            $this->getResponse()->getBody()
        );
    }

    /**
     * Covers Magento_CatalogEvent_Block_Adminhtml_Catalog_Category_Edit_Buttons::addButtons for Edit Event button
     *
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture eventDataFixture
     */
    public function testEditCategoryActionEditEvent()
    {
        $this->dispatch('backend/admin/catalog_category/edit/id/3');
        $this->assertContains(
            'onclick="setLocation(\'http://localhost/index.php/backend/admin/catalog_event/edit/id/',
            $this->getResponse()->getBody()
        );
    }

    public static function eventDataFixture()
    {
        /** @var $event Magento_CatalogEvent_Model_Event */
        $event = Mage::getModel('Magento_CatalogEvent_Model_Event');
        $event->setStoreId(0);
        $event->setCategoryId('3');
        $event->setStoreDateStart(date('Y-m-d H:i:s'))->setStoreDateEnd(date('Y-m-d H:i:s', time() + 3600));
        $event->save();
    }
}
