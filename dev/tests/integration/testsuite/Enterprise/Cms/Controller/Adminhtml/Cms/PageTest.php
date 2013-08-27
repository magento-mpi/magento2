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
 * @magentoAppArea adminhtml
 */
class Enterprise_Cms_Controller_Adminhtml_Cms_PageTest extends Magento_Backend_Utility_Controller
{
    /**
     * Checks if Enterprise_Cms_Block_Adminhtml_Cms_Page::_prepareLayout finds child 'grid' block
     */
    public function testIndexAction()
    {
        $this->dispatch('backend/admin/cms_page/index');
        $content = $this->getResponse()->getBody();
        $this->assertContains('Version Control', $content);
    }
}
