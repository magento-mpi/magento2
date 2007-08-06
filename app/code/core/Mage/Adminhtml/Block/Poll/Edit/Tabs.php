<?php
/**
 * Admin poll left menu
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Adminhtml_Block_Poll_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('poll_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Poll Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => __('Poll Information'),
            'title'     => __('Poll Information'),
            'content'   => $this->getLayout()->createBlock('adminhtml/poll_edit_tab_form')->toHtml(),
        ))
        ;

        $this->addTab('answers_section', array(
                'label'     => __('Poll Answers'),
                'title'     => __('Poll Answers'),
                'content'   => $this->getLayout()->createBlock('adminhtml/poll_edit_tab_answers')
                                ->append($this->getLayout()->createBlock('adminhtml/poll_edit_tab_answers_list'))
                                ->toHtml(),
                'active'    => ( $this->getRequest()->getParam('tab') == 'answers_section' ) ? true : false,
            ));
        return parent::_beforeToHtml();
    }
}