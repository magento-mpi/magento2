<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page;

use Magento\Customer\Model\Context;

/**
 * @magentoAppArea adminhtml
 */
class RevisionTest extends \Magento\Backend\Utility\Controller
{
    /**
     * @magentoDataFixture Magento/Cms/_files/pages.php
     */
    public function testPreviewAction()
    {
        /** @var $page \Magento\Cms\Model\Page */
        $page = $this->_objectManager->create('Magento\Cms\Model\Page');
        $page->load('page100', 'identifier');
        // fixture cms/page
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
        $storeId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\StoreManagerInterface'
        )->getDefaultStoreView();
        // fixture design_change
        $context = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Framework\App\Http\Context');
        $context->setValue(Context::CONTEXT_AUTH, false, false);

        $this->getRequest()->setParam('preview_selected_store', $storeId);

        /** @var $page \Magento\Cms\Model\Page */
        $page = $this->_objectManager->create('Magento\Cms\Model\Page');
        $page->load('page100', 'identifier');
        // fixture cms/page
        $this->getRequest()->setPost('page_id', $page->getId());

        $this->dispatch('backend/admin/cms_page_revision/drop/');
        $this->assertContains('static/frontend/Magento/plushe', $this->getResponse()->getBody());
        $this->assertContains($page->getContent(), $this->getResponse()->getBody());
    }
}
