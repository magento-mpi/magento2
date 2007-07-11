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
        $sections = $config->getNode('admin/configuration/sections')->children();
        foreach ($sections as $code=>$section) {
            if (empty($current)) {
                $current = $code;
                $this->getRequest()->setParam('section', $current);
            }
            $label = __((string)$section->label);
            if ($code == $current) {
                $this->_addBreadcrumb($label);
            }

            $this->addTab($code, array(
                'label'     => $label,
                'url'       => Mage::getUrl('*/*/*', array('section'=>$code)),
                'class'     => ($code == $current) ? 'active' : '',
            ));
        }
        return $this;
    }
}
