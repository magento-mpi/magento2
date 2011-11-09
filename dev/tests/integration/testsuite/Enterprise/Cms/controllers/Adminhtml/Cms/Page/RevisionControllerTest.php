<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Cms
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Enterprise_Cms
 */
class Enterprise_Cms_Adminhtml_Cms_Page_RevisionControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @magentoDataFixture Mage/Core/_files/design_change.php
     * @magentoDataFixture Mage/Cms/_files/pages.php
     */
    public function testDropAction()
    {
        $storeId = Mage::app()->getAnyStoreView(); // fixture design_change
        $this->getRequest()->setParam('preview_selected_store', $storeId);

        $page = new Mage_Cms_Model_Page;
        $page->load('page100', 'identifier'); // fixture cms/page
        $this->getRequest()->setParam('page_id', $page->getId());

        $this->dispatch('adminhtml/cms_page_revision/drop/');
        $this->assertContains('skin/frontend/default/modern/default', $this->getResponse()->getBody());
    }
}
