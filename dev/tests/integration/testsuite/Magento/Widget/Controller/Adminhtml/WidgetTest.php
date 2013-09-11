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
     * Partially covers \Magento\Widget\Block\Adminhtml\Widget\Options::_addField()
     */
    public function testLoadOptionsAction()
    {
        $this->getRequest()->setPost('widget', '{"widget_type":"\Magento\Cms\Block\Widget\Page\Link","values":{}}');
        $this->dispatch('backend/admin/widget/loadOptions');
        $output = $this->getResponse()->getBody();
        //searching for label with text "CMS Page"
        $this->assertContains('data-ui-id="wysiwyg-widget-options-fieldset-element-label-parameters-page-id-label" >'
            . '<span>CMS Page', $output);
    }
}
