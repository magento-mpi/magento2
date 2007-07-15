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
class Mage_Adminhtml_Block_System_Config_Gwstree extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        #$this->setTemplate('adminhtml/widget/tabs.phtml');
        $this->setId('system_config_gwstree');
        $this->setDestElementId('system_config_form');
        #$this->setTitle(__('-={ gWs }=-'));
    }
    
    public function initTabs()
    {
        $curWebsite = $this->getRequest()->getParam('website');
        $curStore = $this->getRequest()->getParam('store');
        
        $websitesConfig = Mage::getConfig()->getNode('websites');
        $storesConfig = Mage::getConfig()->getNode('stores');

        $this->addTab('default', array(
            'label'  => 'Default',
            'url'    => Mage::getUrl('*/*/*', array('_current'=>true, 'website'=>null, 'store'=>null)),
            'class' => 'default'.(!$curWebsite && !$curStore ? ' active' : ''),
        ));
        
        foreach ($websitesConfig->children() as $wCode=>$wConfig) {
            $this->addTab('website_'.$wCode, array(
                'label' => (string)$wConfig->descend('system/website/name'),
                'url'   => Mage::getUrl('*/*/*', array('_current'=>true, 'website'=>$wCode, 'store'=>null)),
                'class' => 'website'.($curWebsite===$wCode ? ' active' : ''),
            ));
            foreach ($wConfig->descend('system/stores')->children() as $sCode=>$sId) {
                $this->addTab('store_'.$sCode, array(
                    'label' => (string)$storesConfig->descend($sCode.'/system/store/name'),
                    'url'   => Mage::getUrl('*/*/*', array('_current'=>true, 'website'=>null, 'store'=>$sCode)),
                    'class' => 'store'.($curStore===$sCode ? ' active' : ''),
                ));
            }
        }

        return $this;
    }
}