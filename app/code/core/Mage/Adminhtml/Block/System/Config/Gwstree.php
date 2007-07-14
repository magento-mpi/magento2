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
        
        $this->addTab('global', array(
            'label'  => 'Global',
            'url'    => Mage::getUrl('*/*/*', array('_current'=>true, 'website'=>null, 'store'=>null)),
            'class' => (!$curWebsite && !$curStore ? 'active' : ''),
        ));
        
        foreach (Mage::getConfig()->getNode('websites')->children() as $wCode=>$wConfig) {
            $this->addTab('website_'.$wCode, array(
                'label' => $wCode,
                'url'   => Mage::getUrl('*/*/*', array('_current'=>true, 'website'=>$wCode, 'store'=>null)),
                'class' => ($curWebsite===$wCode ? 'active' : ''),
            ));
            foreach ($wConfig->descend('system/stores')->children() as $sCode=>$sId) {
                $this->addTab('store_'.$sCode, array(
                    'label' => $sCode,
                    'url'   => Mage::getUrl('*/*/*', array('_current'=>true, 'website'=>null, 'store'=>$sCode)),
                    'class' => ($curStore===$sCode ? 'active' : ''),
                ));
            }
        }

        return $this;
    }
}