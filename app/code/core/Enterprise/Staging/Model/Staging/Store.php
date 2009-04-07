<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Staging store model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Model_Staging_Store extends Mage_Core_Model_Abstract
{
    const EXCEPTION_LOGIN_NOT_CONFIRMED       = 1;
    const EXCEPTION_INVALID_LOGIN_OR_PASSWORD = 2;

    /**
     * Staging Object
     * @var Enterprise_Staging_Model_Staging
     */
    protected $_staging;

    /**
     * Staging Website Object
     * @var Enterprise_Staging_Model_Staging_Website
     */
    protected $_stagingWebsite;

    /**
     * Staging Store Group Object
     * @var Enterprise_Staging_Model_Staging_Store_Group
     */
    protected $_stagingStoreGroup;

    /**
     * Staging Items Collection
     * @var Enterprise_Staging_Model_Mysql4_Staging_Item_Collection
     */
    protected $_items;

    /**
     * Dataset Items Collection
     * @var Enterprise_Staging_Model_Mysql4_Dataset_Item_Collection
     */
    protected $_datasetItems;

    /**
     * Constructor (init resource model)
     */
    protected function _construct()
    {
        $this->_init('enterprise_staging/staging_store');
    }

    /**
     * Declare staging
     *
     * @param   Enterprise_Staging_Model_Staging $staging
     * @return  Enterprise_Staging_Model_Staging_Store
     */
    public function setStaging(Enterprise_Staging_Model_Staging $staging)
    {
        $this->_staging = $staging;
        $this->setStagingId($staging->getId());
        return $this;
    }

    /**
     * Retrieve staging model object
     *
     * @return Enterprise_Staging_Model_Staging
     */
    public function getStaging()
    {
        if (is_null($this->_staging) && ($stagingId = $this->getStagingId())) {
            $staging = Mage::getModel('enterprise_staging/staging');
            $staging->load($stagingId);
            $this->setStaging($staging);
        }
        return $this->_staging;
    }

    /**
     * Declare staging website
     *
     * @param   Enterprise_Staging_Model_Staging_Website $website
     * @return  Enterprise_Staging_Model_Staging_Store
     */
    public function setStagingWebsite(Enterprise_Staging_Model_Staging_Website $website)
    {
        $this->_stagingWebsite = $website;
        $this->setStagingWebsiteId($website->getId());
        return $this;
    }

    /**
     * Retrieve staging model object
     *
     * @return Enterprise_Staging_Model_Staging
     */
    public function getStagingWebsite()
    {
        if (is_null($this->_stagingWebsite) && ($websiteId = $this->getStagingWebsiteId())) {
            $website = Mage::getModel('enterprise_staging/staging_website');
            $website->load($websiteId);
            $this->setStagingWebsite($website);
        }
        return $this->_stagingWebsite;
    }

    /**
     * Retrieve staging items collection with setted current staging store filter
     *
     * @return Enterprise_Staging_Model_Mysql4_Staging_Item_Collection
     */
    public function getItemsCollection()
    {
        if (is_null($this->_items)) {
            $this->_items = Mage::getResourceModel('enterprise_staging/staging_item_collection')
                ->addStagingStoreFilter($this->getId());

            if ($this->getId()) {
                foreach ($this->_items as $item) {
                    $item->setStagingStore($this);
                }
            }
        }
        return $this->_items;
    }

    /**
     * Retrieve staging item ids array
     *
     * @return array
     */
    public function getItemIds()
    {
        if ($this->hasData('item_ids')) {
            $ids = $this->getData('item_ids');
            if (!is_array($ids)) {
                $ids = !empty($ids) ? explode(',', $ids) : array();
                $this->setData('item_ids', $ids);
            }
        } else {
            $ids = array();
            foreach ($this->getItemsCollection() as $item) {
                $ids[] = $item->getId();
            }
            $this->setData('item_ids', $ids);
        }
        return $this->getData('item_ids');
    }

    /**
     * Add staging item into staging store items collection
     *
     * @param Enterprise_Staging_Model_Staging_Item $item
     *
     * @return Enterprise_Staging_Model_Staging_Store
     */
    public function addItem(Enterprise_Staging_Model_Staging_Item $item)
    {
        $item->setStagingStore($this);
        if (!$item->getId()) {
            $this->getItemsCollection()->addItem($item);
        }
        return $this;
    }

    /**
     * Retrieve dataset items collection with ability to set "ignore backend items" filter
     *
     * @param   boolean $ignoreBackendFlag
     * @return  Enterprise_Staging_Model_Mysql4_Dataset_Item_Collection
     */
    public function getDatasetItemsCollection($ignoreBackendFlag = null)
    {
        if (is_null($this->_datasetItems)) {
            $this->_datasetItems = Mage::getResourceSingleton('enterprise_staging/dataset_item_collection')
                ->addBackendFilter($ignoreBackendFlag);
            if ($this->getDatasetId()) {
               $this->_datasetItems->addDatasetFilter($this->getDatasetId());
            }
        }
        return $this->_datasetItems;
    }

    /**
     * Retrieve dataset item ids array
     *
     * @return array
     */
    public function getDatasetItemIds()
    {
        $ids = array();
        foreach($this->getItemsCollection() as $item) {
            $ids[] = $item->getDatasetItemId();
        }
        return $ids;
    }

    /**
     * Retrieve master store instance
     *
     * @return Mage_Core_Model_Store
     */
    public function getMasterStore()
    {
        $masterStoreId = $this->getMasterStoreId();
        if (!is_null($masterStoreId)) {
            return Mage::app()->getStore($masterStoreId);
        } else {
            return false;
        }
    }

    /**
     * Retrieve slave store instance
     *
     * @return Mage_Core_Model_Store
     */
    public function getSlaveStore()
    {
        $slaveStoreId = $this->getSlaveStoreId();
        if (!is_null($slaveStoreId)) {
            return Mage::app()->getStore($slaveStoreId);
        } else {
            return false;
        }
    }

    /**
     * Update an attribute value
     *
     * @param string    $attribute
     * @param string    $value
     *
     * @return Enterprise_Staging_Model_Staging_Store
     */
    public function updateAttribute($attribute, $value)
    {
        $this->getResource()->updateAttribute($this, $attribute, $value);
        return $this;
    }

    /**
     * init Enterprise_Staging_Model_Staging_Store object by store id
     *
     * @param int $id
     * @return Enterprise_Staging_Model_Staging_Store
     */
    public function loadBySlaveStoreId($id)
    {
        $this->getResource()->loadBySlaveStoreId($this, $id);

        return $this;
    }

    /**
     * sync store
     *
     * @param Mage_Core_Model_Store $store
     * @return Enterprise_Staging_Model_Staging_Store
     */
    public function syncWithStore(Mage_Core_Model_Store $store)
    {
        $this->getResource()->syncWithStore($this, $store);

        return $this;
    }
}