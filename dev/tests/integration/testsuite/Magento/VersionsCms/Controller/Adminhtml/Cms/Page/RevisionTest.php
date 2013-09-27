<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page;

class RevisionTest extends \Magento\Backend\Utility\Controller
{
    /**
     * @magentoDataFixture Magento/Cms/_files/pages.php
     */
    public function testPreviewAction()
    {
        /** @var $page \Magento\Cms\Model\Page */
        $page = $this->_objectManager->create('Magento\Cms\Model\Page');
        $page->load('page100', 'identifier'); // fixture cms/page
        $this->getRequest()->setPost('page_id', $page->getId());
        $this->dispatch('backend/admin/cms_page_revision/preview/');
        $body = $this->getResponse()->getBody();
        $this->assertContains('<input id="preview_selected_revision"', $body);
        $this->assertNotContains('<select name="revision_switcher" id="revision_switcher">', $body);
    }

    /**
     * @magentoDataFixture Magento/Core/_files/design_change.php
     * @magentoDataFixture Magento/Cms/_files/pages.php
     */
    public function testDropAction()
    {
        $storeId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\StoreManagerInterface')->getAnyStoreView(); // fixture design_change
        $this->getRequest()->setParam('preview_selected_store', $storeId);

        /** @var $page \Magento\Cms\Model\Page */
        $page = $this->_objectManager->create('Magento\Cms\Model\Page');
        $page->load('page100', 'identifier'); // fixture cms/page
        $this->getRequest()->setPost('page_id', $page->getId());

        $this->dispatch('backend/admin/cms_page_revision/drop/');
        $this->assertContains('static/frontend/magento_blank', $this->getResponse()->getBody());
        $this->assertContains($page->getContent(), $this->getResponse()->getBody());
    }
}
