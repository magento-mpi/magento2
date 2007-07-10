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
class Mage_Adminhtml_Block_System_Config_Tab_General extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('config_tabs');
        $this->setDestElementId('config_edit_form');
    }
    
    protected function _beforeToHtml()
    {
        Varien_Profiler::start('configTab');
        $this->addTab('global', array(
            'label'     => __('global'),
            'title'     => __('global title'),
            'content'   => $this->getLayout()->createBlock('adminhtml/config_tab_global')->toHtml(),
            'active'    => true
        ));

        Varien_Profiler::stop('configTab');
        return parent::_beforeToHtml();
    }
}
