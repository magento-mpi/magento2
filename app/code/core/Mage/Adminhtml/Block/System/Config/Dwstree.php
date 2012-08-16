<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * admin customer left menu
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Config_Dwstree extends Mage_Adminhtml_Block_Widget_Tabs
{

    protected $_template = 'widget/tabs.phtml';

    protected function _construct()
    {
        parent::_construct();
        #
        $this->setId('system_config_dwstree');
        $this->setDestElementId('system_config_form');
    }

    public function initTabs()
    {
        $section = $this->getRequest()->getParam('section');

        $curWebsite = $this->getRequest()->getParam('website');
        $curStore = $this->getRequest()->getParam('store');

        $websitesConfig = Mage::getConfig()->getNode('websites');
        $storesConfig = Mage::getConfig()->getNode('stores');

        $this->addTab('default', array(
            'label'  => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Default Config'),
            'url'    => $this->getUrl('*/*/*', array('section'=>$section)),
            'class' => 'default',
        ));

        foreach ($websitesConfig->children() as $wCode=>$wConfig) {
            $wName = (string)$wConfig->descend('system/website/name');
            $wUrl = $this->getUrl('*/*/*', array('section'=>$section, 'website'=>$wCode));
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
                    'url'   => $this->getUrl('*/*/*', array('section'=>$section, 'website'=>$wCode, 'store'=>$sCode)),
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
