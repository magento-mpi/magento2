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

class Enterprise_Cms_Adminhtml_Cms_PageControllerTest extends Mage_Adminhtml_Utility_Controller
{
    /**
     * Checks if Enterprise_Cms_Block_Adminhtml_Cms_Page_Edit::_prepareLayout finds child 'form' block
     */
    public function testEditAction()
    {
        $this->dispatch('admin/cms_page/edit/page_id/3');
        $content = $this->getResponse()->getBody();
        $this->assertContains('onclick="pagePreviewAction()"', $content);
        $this->assertContains('function pagePreviewAction() {', $content);
    }

    /**
     * Checks if Enterprise_Cms_Block_Adminhtml_Cms_Page::_prepareLayout finds child 'grid' block
     */
    public function testIndexAction()
    {
        $this->dispatch('admin/cms_page/index');
        $content = $this->getResponse()->getBody();
        $this->assertContains('Version Control', $content);
    }
}
