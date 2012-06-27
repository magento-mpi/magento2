<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Widget_Adminhtml_Widget_InstanceControllerTest extends Mage_Adminhtml_Utility_Controller
{
    protected function setUp()
    {
        parent::setUp();

        $this->getRequest()->setParam('type', 'Mage_Cms_Block_Widget_Page_Link');
        $this->getRequest()->setParam('package_theme', 'default-default');
    }

    public function testEditAction()
    {
        $this->markTestIncomplete('MAGETWO-1587');
        $this->dispatch('admin/widget_instance/edit');
        $this->assertContains('<option value="Mage_Cms_Block_Widget_Page_Link" selected="selected">',
            $this->getResponse()->getBody()
        );
    }

    public function testBlocksAction()
    {
        $this->markTestIncomplete('MAGETWO-1587');
        $this->dispatch('admin/widget_instance/blocks');
        $this->assertStringStartsWith('<select name="block" id=""', $this->getResponse()->getBody());
    }

    public function testTemplateAction()
    {
        $this->markTestIncomplete('MAGETWO-1587');
        $this->dispatch('admin/widget_instance/template');
        $this->assertStringStartsWith('<select name="template" id=""', $this->getResponse()->getBody());
    }
}
