<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Widget_Controller_Adminhtml_WidgetTest extends Magento_Backend_Utility_Controller
{
    /**
     * Partially covers Magento_Widget_Block_Adminhtml_Widget_Options::_addField()
     */
    public function testLoadOptionsAction()
    {
        $this->getRequest()->setPost('widget', '{"widget_type":"Magento_Cms_Block_Widget_Page_Link","values":{}}');
        $this->dispatch('backend/admin/widget/loadOptions');
        $output = $this->getResponse()->getBody();
        //searching for label with text "CMS Page"
        $this->assertContains('data-ui-id="wysiwyg-widget-options-fieldset-element-label-parameters-page-id-label" >'
            . '<span>CMS Page', $output);
    }
}
