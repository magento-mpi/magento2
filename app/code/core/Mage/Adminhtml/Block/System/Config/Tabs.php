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
    
    public function initTabs()
    {
        $this->_addBreadcrumb(__('Config'), null, Mage::getUrl('*/*'));
        $config = Mage::getSingleton('adminhtml/system_config');
        $current = $this->getRequest()->getParam('section');

        $sections = Mage::getResourceModel('core/config_field_collection')
            ->addFieldToFilter('path', array('nlike'=>'%/%'))
            ->loadData();
        
        foreach ($sections as $section) {
            $code = $section->getPath();
            if (empty($current)) {
                $current = $code;
                $this->getRequest()->setParam('section', $current);
            }
            $label = __($section->getFrontendLabel());
            if ($code == $current) {
                if (!$this->getRequest()->getParam('website') && !$this->getRequest()->getParam('store')) {
                    $this->_addBreadcrumb($label);
                } else {
                    $this->_addBreadcrumb($label, '', Mage::getUrl('*/*/*', array('section'=>$code)));
                }
            }

            $this->addTab($code, array(
                'label'     => $label,
                'url'       => Mage::getUrl('*/*/*', array('_current'=>true, 'section'=>$code)),
            ));
            if ($code == $current) {
                $this->setActiveTab($code);
            }
        }
        return $this;
    }
}
