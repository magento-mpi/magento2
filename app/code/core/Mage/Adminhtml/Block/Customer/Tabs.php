<?php
/**
 * admin customer left menu
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Customer_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_info_tabs');
        $this->setDestElementId('customer_edit_form');
    }
    
    protected function _beforeToHtml()
    {
        $this->addTab('account', array(
            'label'     => __('customer account'),
            'title'     => __('customer account title'),
            'content'   => $this->getLayout()->createBlock('adminhtml/customer_tab_account')->toHtml(),
            'active'    => true
        ));

        $this->addTab('addresses', array(
            'label'     => __('customer addresses'),
            'title'     => __('customer addresses title'),
            'content'   => $this->getLayout()->createBlock('adminhtml/customer_tab_addresses')->toHtml(),
        ));
        
        return parent::_beforeToHtml();
    }
}
