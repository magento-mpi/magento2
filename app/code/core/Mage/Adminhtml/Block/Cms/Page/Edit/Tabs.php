<?php
/**
 * Admin page left menu
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Adminhtml_Block_Cms_Page_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Page Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('main_section', array(
            'label'     => __('General Information'),
            'title'     => __('General Information'),
            'content'   => $this->getLayout()->createBlock('adminhtml/cms_page_edit_tab_main')->toHtml(),
            'active'    => true
        ));

        $this->addTab('meta_section', array(
            'label'     => __('Meta Data'),
            'title'     => __('Meta Data'),
            'content'   => $this->getLayout()->createBlock('adminhtml/cms_page_edit_tab_meta')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }

}
