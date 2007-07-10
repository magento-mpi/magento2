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
class Mage_Adminhtml_Block_System_Config_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('system_config_tabs');
        $this->setDestElementId('system_config_form');
        $this->setTitle(__('Customer Information'));
    }
    
    protected function _beforeToHtml()
    {

        $this->addTab('addresses', array(
            'label'     => __('Addresses'),
            'content'   => $this->getLayout()->createBlock('adminhtml/system_config_tab_general')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
    
    public function bindBreadcrumbs($breadcrumbs)
    {
        if ($this->_websiteCode) {
            $this->_addBreadcrumb(__('config'), __('config title'), Mage::getUrl('adminhtml/system_config'));
            if ($this->_storeCode) {
                $this->_addBreadcrumb(__($this->_websiteCode), '', Mage::getUrl('adminhtml/system_config',array('website'=>$this->_websiteCode)));
                $this->_addBreadcrumb(($this->_storeCode == 1) ? __('new store') :__($this->_storeCode), '');
            }
            else {
                $this->_addBreadcrumb(($this->_websiteCode == 1) ? __('new website') :__($this->_websiteCode), '');
            }
        }
        else {
            $this->_addBreadcrumb(__('config'), __('config title'));
            $this->_addBreadcrumb(__('global'), __('global title'));
        }
        return $this;
    }
}
