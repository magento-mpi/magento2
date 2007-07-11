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
        $this->setTitle(__('GWS'));
    }
    
    public function initTabs()
    {
        $this->addTab('global', array(
            'label'     => 'Global',
            'content'   => 'global',
        ));
        $websitesCollection = Mage::getResourceModel('core/website_collection')->load();
        $storesCollection = Mage::getResourceModel('core/store_collection')->load();
        $websites = array();
        foreach ($websitesCollection->getItems() as $website) {
            $websites[$website->getId()] = $website;
        }
        print_r($websites);
        foreach ($storesCollection->getItems() as $store) {
            $stores = $websites[$store->getWebsiteId()]->getStores();
            if (empty($stores)) {
                $stores = array();
            }
            $stores[$store->getId()] = $store;
            $websites[$store->getWebsiteId()]->getStores($stores);
        }
        foreach ($websites as $website) {
            $this->addTab($website->getCode(), array(
                'label' => $website->getCode(),
                'content' => $website->getCode()
            ));
            foreach ($website->getStores() as $store) {
                $this->addTab($store->getCode(), array(
                    'label' => $store->getCode(),
                    'content' => $store->getCode()
                ));
            }
        }
        return $this;
    }
}