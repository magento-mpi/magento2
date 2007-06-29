<?php
/**
 * admin config left menu
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Moshe Gurvich <moshe@varien.com>
 */
class Mage_Adminhtml_Block_System_Config_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('config_tabs');
        $this->setDestElementId('config_edit_form');
    }
    
    protected function _beforeToHtml()
    {
        Varien_Profiler::start('configCard');
        $this->addTab('global', array(
            'label'     => __('global'),
            'title'     => __('global title'),
            //'content'   => $this->getLayout()->createBlock('adminhtml/config_tab')->toHtml(),
            'content'   => 'empty',
            'active'    => true
        ));

        $this->addTab('website1', array(
            'label'     => __('website1'),
            'title'     => __('website1 title'),
            //'content'   => $this->getLayout()->createBlock('adminhtml/config_tab')->toHtml(),
            'content'   => 'website1',
            'active'    => true
        ));
        
        $this->addTab('store 1', array(
            'label'     => __('store 1'),
            'title'     => __('store 1 title'),
            //'content'   => $this->getLayout()->createBlock('adminhtml/config_tab')->toHtml(),
            'content'   => 'website1',
            'active'    => true
        ));
        
        $this->addTab('new store', array(
            'label'     => __('website1'),
            'title'     => __('website1 title'),
            //'content'   => $this->getLayout()->createBlock('adminhtml/config_tab')->toHtml(),
            'content'   => 'website1',
            'active'    => true
        ));
        
        $this->addTab('website1', array(
            'label'     => __('website1'),
            'title'     => __('website1 title'),
            //'content'   => $this->getLayout()->createBlock('adminhtml/config_tab')->toHtml(),
            'content'   => 'website1',
            'active'    => true
        ));
        
        $this->addTab('website1', array(
            'label'     => __('website1'),
            'title'     => __('website1 title'),
            //'content'   => $this->getLayout()->createBlock('adminhtml/config_tab')->toHtml(),
            'content'   => 'website1',
            'active'    => true
        ));
        
        Varien_Profiler::stop('configCard');
        return parent::_beforeToHtml();
    }
}
