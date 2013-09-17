<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Widget_Controller_Adminhtml_Widget_InstanceTest extends Magento_Backend_Utility_Controller
{
    protected function setUp()
    {
        parent::setUp();

        $theme = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_View_DesignInterface')
            ->setDefaultDesignTheme()
            ->getDesignTheme();
        $this->getRequest()->setParam('type', 'Magento_Cms_Block_Widget_Page_Link');
        $this->getRequest()->setParam('theme_id', $theme->getId());
    }

    public function testEditAction()
    {
        $this->dispatch('backend/admin/widget_instance/edit');
        $this->assertContains('<option value="Magento_Cms_Block_Widget_Page_Link" selected="selected">',
            $this->getResponse()->getBody()
        );
    }

    public function testBlocksAction()
    {
        $this->dispatch('backend/admin/widget_instance/blocks');
        $this->assertStringStartsWith('<select name="block" id=""', $this->getResponse()->getBody());
    }

    public function testTemplateAction()
    {
        $this->dispatch('backend/admin/widget_instance/template');
        $this->assertStringStartsWith('<select name="template" id=""', $this->getResponse()->getBody());
    }
}
