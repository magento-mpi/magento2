<?php
/**
 * Currency edit tabs
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_System_Currency_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('currency_edit_tabs');
        $this->setDestElementId('currency_edit_form');
        $this->setTitle(__('Currency'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('general', array(
            'label'     => __('General Information'),
            'content'   => $this->getLayout()->createBlock('adminhtml/system_currency_edit_tab_main')->toHtml(),
            'active'    => true
        ));

        $this->addTab('currency_rates', array(
            'label'     => __('Rates'),
            'content'   => $this->getLayout()->createBlock('adminhtml/system_currency_edit_tab_rates')->toHtml(),
        ));
        
        return parent::_beforeToHtml();
    }
}