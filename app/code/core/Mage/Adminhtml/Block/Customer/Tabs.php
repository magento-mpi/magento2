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
        Varien_Profiler::start('customer/tabs/account/block');
        $block = $this->getLayout()->createBlock('adminhtml/customer_tab_account');
        Varien_Profiler::stop('customer/tabs/account/block');
        
        Varien_Profiler::start('customer/tabs/account/html');
        $html = $block->toHtml();
        Varien_Profiler::stop('customer/tabs/account/html');
        
        $this->addTab('account', array(
            'label'     => __('customer account'),
            'title'     => __('customer account title'),
            'content'   => $html,
            'active'    => true
        ));

        Varien_Profiler::start('customer/tabs/addresses/block');
        $block = $this->getLayout()->createBlock('adminhtml/customer_tab_addresses');
        Varien_Profiler::stop('customer/tabs/addresses/block');
        
        Varien_Profiler::start('customer/tabs/addresses/html');
        $html = $block->toHtml();
        Varien_Profiler::stop('customer/tabs/addresses/html');
        
        $this->addTab('addresses', array(
            'label'     => __('customer addresses'),
            'title'     => __('customer addresses title'),
            'content'   => $html,
        ));
        
        $this->addTab('orders', array(
            'label'     => __('Customer orders'),
            'title'     => __('Customer orders title'),
            'content'   => 'orders',
        ));
        
        $this->addTab('reviews', array(
            'label'     => __('Customer reviews'),
            'title'     => __('Customer reviews title'),
            'content'   => 'reviews',
        ));
        
        $this->addTab('wishlist', array(
            'label'     => __('customer wishlist'),
            'title'     => __('customer wishlist title'),
            'content'   => 'wishlist',
        ));
        
        $this->addTab('newsletter', array(
            'label'     => __('customer newsletter'),
            'title'     => __('customer newsletter title'),
            'content'   => 'newsletter',
        ));        
        Varien_Profiler::stop('customer/tabs');
        return parent::_beforeToHtml();
    }
}
