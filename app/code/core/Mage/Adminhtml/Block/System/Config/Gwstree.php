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
            'label'  => __('Default config'),
            'url'    => Mage::getUrl('*/*/*', array('_current'=>true, 'website'=>null, 'store'=>null)),
            'class' => 'default',
        )); 
        
        foreach ($websitesConfig->children() as $wCode=>$wConfig) {
            $wName = (string)$wConfig->descend('system/website/name');
            $wUrl = Mage::getUrl('*/*/*', array('_current'=>true, 'website'=>$wCode, 'store'=>null));
            $this->addTab('website_'.$wCode, array(
                'label' => $wName,
                'url'   => $wUrl,
                'class' => 'website',
            ));
            if ($curWebsite===$wCode) {
                if ($curStore) {
                    $this->_addBreadcrumb($wName, '', $wUrl);
                } else {
                    $this->_addBreadcrumb($wName);
                }
            }
            foreach ($wConfig->descend('system/stores')->children() as $sCode=>$sId) {
                $sName = (string)$storesConfig->descend($sCode.'/system/store/name');
                $this->addTab('store_'.$sCode, array(
                    'label' => $sName,
                    'url'   => Mage::getUrl('*/*/*', array('_current'=>true, 'website'=>$wCode, 'store'=>$sCode)),
                    'class' => 'store',
                ));
                if ($curStore===$sCode) {
                    $this->_addBreadcrumb($sName);
                }
            }
        }
        if ($curStore) {
            $this->setActiveTab('store_'.$curStore);
        } elseif ($curWebsite) {
            $this->setActiveTab('website_'.$curWebsite);
        } else {
            $this->setActiveTab('default');
        }

        return $this;
    }
}