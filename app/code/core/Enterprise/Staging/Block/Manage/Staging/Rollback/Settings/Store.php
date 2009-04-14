<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Staging rollback settings of staging website type block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Manage_Staging_Rollback_Settings_Store extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Keep main translate helper instance
     *
     * @var object
     */    
    protected $helper;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->helper = Mage::helper('enterprise_staging');
    }
    
    /**
     * Prepare form before rendering HTML
     *
     * @return Enterprise_Staging_Block_Manage_Staging_Edit_Tabs_Item
     */
    protected function _prepareForm()
    {

        $event            = $this->getEvent();
        $backup          = $this->getBackup(); 
        $staging          = $this->getStaging();
        $items            = $staging->getItemsCollection();
        $masterWebsites   = $this->getWebsiteCollection();
        $stores           = $this->getAllStoresCollection();
        $stagingWebsites  = $staging->getWebsitesCollection();
        $mapperUsedItems  = $this->getMapper()->getUsedItems();
        $mapperWebSites   = $this->getMapper()->getAllUsedWebsites();
        
        $form = new Varien_Data_Form();
        
        foreach($mapperWebSites AS $mapperWebSiteInfo){
            if (isset($mapperWebSiteInfo["master_website"])){
        
                foreach($mapperWebSiteInfo["master_website"] AS $toSiteId => $fromSiteId){
                    
                    $siteInfo = $masterWebsites->getItemByColumnValue("website_id", $toSiteId);
                    $form = $this->_initWebSiteForm($form, $fromSiteId , $toSiteId);
                    
                    if (isset($mapperWebSiteInfo["stores"][$toSiteId])){
                        foreach($stores AS $storeInfo){
                            if ($fromStoreId = array_search($storeInfo->getId(), $mapperWebSiteInfo["stores"][$toSiteId])){
                                $id = $fromSiteId . '-' . $toSiteId;
                                 
                                $form = $this->_initStoreForm($form, $id, $fromStoreId, $storeInfo->getId());
                            }                       
                        }
                    }
                }
            }
        } 
        
        $this->setForm($form);
        return parent::_prepareForm();
    }
    
    /**
     * Init website form
     *
     * @param Mage_Adminhtml_Block_Widget_Form $form
     * @param int $fromSiteId
     * @param int $toSiteId
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _initWebSiteForm($form, $fromSiteId, $toSiteId)
    {
        $id = $fromSiteId . "_" . $toSiteId;
        
        $form->addField("map_from_$id" , 'hidden' , 
            array(
                'name'  => 'map[websites][from][]',
                'value' => $fromSiteId
            )
        );

        $form->addField("map_to_$id" , 'hidden' , 
            array(
                'name'  => 'map[websites][to][]',
                'value' => $toSiteId
            )
        );
        
        return $form;
        
    }

    /**
     * Init Store form
     *
     * @param Mage_Adminhtml_Block_Widget_Form $form
     * @param int $website_id
     * @param int $fromStoreId
     * @param int $toStoreId
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    
    protected function _initStoreForm($form, $website_id, $fromStore, $toStore)
    {
        $fieldset_id = $website_id . '_' . $fromStore . '_' . $toStore;
        //$fieldset = $form->addFieldset('store_website_'.$fieldset_id, array());
        
        $form->addField("map_store_from_$fieldset_id" , 'hidden' , 
            array(
                'name'  => "map[stores][$website_id][from][]",
                'value' => $fromStore
            )
        );

        $form->addField("map_store_to_$fieldset_id" , 'hidden' , 
            array(
                'name'  => "map[stores][$website_id][to][]",
                'value' => $toStore
            )
        );
        
        return $form;
    }
    
    /**
     * Retrieve currently edited backup object
     *
     * @return Enterprise_Staging_Model_Staging_Backup
     */
    public function getBackup()
    {
        if (!($this->getData('staging_backup') instanceof Enterprise_Staging_Model_Staging_Backup)) {
            $this->setData('staging_backup', Mage::registry('staging_backup'));
        }
        return $this->getData('staging_backup');
    }
        
    /**
     * Retrieve event 
     *
     * @return Enterprise_Staging_Block_Manage_Staging-Event
     */
    public function getEvent()
    {
        if (!($this->getData('staging_event') instanceof Enterprise_Staging_Model_Staging_Event)) {
            $this->setData('staging_event', Mage::registry('staging_event'));
        }
        return $this->getData('staging_event');
    }
    
    /**
     * Retrieve staging object of current event 
     *
     * @return Enterprise_Staging_Block_Manage_Staging
     */
    public function getStaging()
    {
        return $this->getEvent()->getStaging();
    }
    
    /**
     * return mapper instance
     *
     * @return Enterprise_Staging_Model_Mapper
     */
    public function getMapper()
    {
        return $this->getStaging()->getMapperInstance();
    }
    
    /**
     * return website collection
     *
     * @return object
     */
    public function getWebsiteCollection()
    {
        $collection = Mage::getModel('core/website')->getResourceCollection();

        $staging = $this->getStaging();

        //$collection->addFieldToFilter('is_staging',array('neq'=>1));

        return $collection->load();
    }

    /**
     * return store collection
     *
     * @return object
     */
    public function getAllStoresCollection()
    {
        return Mage::app()->getStores();
    }

    /**
     * return store json
     *
     * @return string
     */
    public function getAllStoresJson()
    {
        $stores = array();
        foreach ($this->getAllStoresCollection() as $store) {
            $stores[$store->getWebsiteId()][] = $store->getData();
        }
        if (!$stores) {
            return '{}';
        } else {
            return Zend_Json::encode($stores);
        }
    }
}