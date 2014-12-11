<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms;

/**
 * @magentoAppArea adminhtml
 */
class PageTest extends \Magento\Backend\Utility\Controller
{
    /**
     * Checks if \Magento\VersionsCms\Block\Adminhtml\Cms\Page::_prepareLayout finds child 'grid' block
     */
    public function testIndexAction()
    {
        $this->dispatch('backend/admin/cms_page/index');
        $content = $this->getResponse()->getBody();
        $this->assertContains('Version Control', $content);
    }
}
