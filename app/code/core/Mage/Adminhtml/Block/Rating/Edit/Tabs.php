<?php
/**
 * Admin rating left menu
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Adminhtml_Block_Rating_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('rating_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Rating Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => __('Rating Information'),
            'title'     => __('Rating Information'),
            'content'   => $this->getLayout()->createBlock('adminhtml/rating_edit_tab_form')->toHtml(),
        ))
        ;

        $this->addTab('answers_section', array(
                'label'     => __('Rating Options'),
                'title'     => __('Rating Options'),
                'content'   => $this->getLayout()->createBlock('adminhtml/rating_edit_tab_options')
                                ->append($this->getLayout()->createBlock('adminhtml/rating_edit_tab_options'))
                                ->toHtml(),
            ));
        return parent::_beforeToHtml();
    }
}