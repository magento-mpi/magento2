<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Widget
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Widget
 */
class Mage_Widget_Adminhtml_WidgetControllerTest extends Mage_Adminhtml_Utility_Controller
{
    /**
     * Partially covers Mage_Widget_Block_Adminhtml_Widget_Options::_addField()
     */
    public function testLoadOptionsAction()
    {
        $this->getRequest()->setPost('widget', '{"widget_type":"Mage_Cms_Block_Widget_Page_Link","values":{}}');
        $this->dispatch('admin/widget/loadOptions');
        $output = $this->getResponse()->getBody();
        $this->assertRegExp('/<label for="options_fieldset[a-z\d]+_page_id">CMS Page/', $output);
    }
}
