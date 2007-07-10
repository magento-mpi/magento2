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
        $this->setTitle(__('Configuration'));
    }
    
    protected function _beforeToHtml()
    {
        $config = Mage::getSingleton('adminhtml/system_config');
        $current = $this->getRequest()->getParam('section');
        foreach ($config->getNode('admin/configuration/sections')->children() as $code=>$section) {
            $this->addTab($code, array(
                'label'     => __((string)$section->label),
                'url'       => Mage::getUrl('*/*/*', array('section'=>$code)),
                'class'     => ($code == $current) ? 'active' : '',
            ));
        }

        return parent::_beforeToHtml();
    }
/*
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
*/
}
