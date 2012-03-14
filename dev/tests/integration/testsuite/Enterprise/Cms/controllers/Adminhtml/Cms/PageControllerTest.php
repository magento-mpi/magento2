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
class Enterprise_Cms_Adminhtml_Cms_PageControllerTest extends Mage_Adminhtml_Utility_Controller
{

    /**
     * @covers Enterprise_Cms_Block_Adminhtml_Cms_Page_Edit::_prepareLayout() in scope of MAGETWO-774
     */
    public function testEditAction()
    {
        $this->dispatch('admin/cms_page/edit/page_id/3');
        $content = $this->getResponse()->getBody();
        $this->assertContains('onclick="pagePreviewAction()"', $content);
        $this->assertContains('function pagePreviewAction() {', $content);
    }

    /**
     * @covers Enterprise_Cms_Block_Adminhtml_Cms_Page::_prepareLayout() in scope of MAGETWO-774
     */
    public function testIndexAction()
    {
        $this->dispatch('admin/cms_page/index');
        $content = $this->getResponse()->getBody();
        $this->assertContains('Version Control', $content);
    }
}
