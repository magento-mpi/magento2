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
 * Staging website model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Model_Staging_Website extends Mage_Core_Model_Abstract
{
	const EXCEPTION_LOGIN_NOT_CONFIRMED       = 1;
    const EXCEPTION_INVALID_LOGIN_OR_PASSWORD = 2;

    /**
     * Staging Stores Collection
     * @var Enterprise_Staging_Model_Staging_Mysql4_Staging_Store_Collection
     */
    protected $_stores;

    /**
     * Staging Items Collection
     * @var Enterprise_Staging_Model_Staging_Mysql4_Staging_Item_Collection
     */
    protected $_items;
    
    /**
     * Staging Items Collection
     * @var Enterprise_Staging_Model_Staging
     */
    protected $_staging;    

    /**
     * Constructor (init resource model)
     */
    protected function _construct()
    {
        $this->_init('enterprise_staging/staging_website');
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
     * Retrieve staging object
     *
     * @return  Enterprise_Staging_Model_Staging
     */
    public function getStaging()
    {
        if (is_object($this->_staging)) {
            return $this->_staging;
        }
        /* try to set staging_id instead whole staging object */
        $_staging = Mage::registry('staging');
        if ($_staging && !is_null($this->_staging) && $_staging->getId() == (int) $this->_staging) {
            return $_staging;
        } elseif($_staging && is_null($this->_staging)) {
            return $_staging; 
        } else {
            if (is_int($this->_staging)) {
                $this->_staging = Mage::getModel('enterprise_staging/staging')->load($this->_staging);
            } else {
                $this->_staging = false;
            }
        }

        return $this->_staging;
    }
    
    /**
     * Authenticate user for frontend view
     *
     * @param  string $login
     * @param  string $password
     * @return true
     * @throws Exception
     */
    public function authenticate($login, $password)
    {
        $this->load($login, 'master_login');
        if ($this->getConfirmation() && $this->isConfirmationRequired()) {
            throw new Exception(Mage::helper('enterprise_staging')->__('This account is not confirmed.'), self::EXCEPTION_LOGIN_NOT_CONFIRMED);
        }
        if (!$this->validatePassword($password)) {
            throw new Exception(Mage::helper('enterprise_staging')->__('Invalid login or password.'), self::EXCEPTION_INVALID_LOGIN_OR_PASSWORD);
        }
        return true;
    }

    /**
     * Set plain and hashed password
     *
     * @param string $password
     * @return Enterprise_Staging_Model_Staging
     */
    public function setMasterPassword($password)
    {
        $this->setData('master_password', $password);
        $this->setMasterPasswordHash($this->hashPassword($password));
        return $this;
    }

    /**
     * Hach customer password
     *
     * @param   string $password
     * @return  string
     */
    public function hashPassword($password, $salt=null)
    {
        return Mage::helper('core')->getHash($password, !is_null($salt) ? $salt : 2);
    }

    /**
     * Retrieve random password
     *
     * @param   int $length
     * @return  string
     */
    public function generatePassword($length=6)
    {
        return substr(md5(uniqid(rand(), true)), 0, $length);
    }

    /**
     * Validate password with salted hash
     *
     * @param string $password
     * @return boolean
     */
    public function validatePassword($password)
    {
        if (!($hash = $this->getMasterPasswordHash())) {
            return false;
        }
        return Mage::helper('core')->validateHash($password, $hash);
    }

    /**
     * Encrypt password
     *
     * @param   string $password
     * @return  string
     */
    public function encryptPassword($password)
    {
        return Mage::helper('core')->encrypt($password);
    }

    /**
     * Decrypt password
     *
     * @param   string $password
     * @return  string
     */
    public function decryptPassword($password)
    {
        return Mage::helper('core')->decrypt($password);
    }

    /**
     * return item ids
     *
     * @return mixed
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
     * Add staging item into staging website items collection
     *
     * @param Enterprise_Staging_Model_Staging_Item $item
     *
     * @return Enterprise_Staging_Model_Staging_Website
     */
    public function addItem(Enterprise_Staging_Model_Staging_Item $item)
    {
        $item->setStagingWebsite($this);
        if (!$item->getId()) {
            $this->getItemsCollection()->addItem($item);
        }
        return $this;
    }

    /**
     * Retrieve staging items collection with setted current staging website filter
     *
     * @return Enterprise_Staging_Model_Mysql4_Staging_Item_Collection
     */
    public function getItemsCollection()
    {
        if (is_null($this->_items)) {
            $this->_items = Mage::getResourceModel('enterprise_staging/staging_item_collection')
                ->addStagingWebsiteFilter($this->getId());

            if ($this->getId()) {
                foreach ($this->_items as $item) {
                    $item->setStagingWebsite($this);
                }
            }
        }
        return $this->_items;
    }

    /**
     * Retrieve dataset items array
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
     * get Master website object
     *
     * @return Mage_Core_Model_Website
     */
    public function getMasterWebsite()
    {
    	$masterWebsiteId = $this->getMasterWebsiteId();
    	if (!is_null($masterWebsiteId)) {
    		return Mage::app()->getWebsite($masterWebsiteId);
    	} else {
    		return false;
    	}
    }

    /**
     * get slave website object collection
     *
     * @return Mage_Core_Model_Website
     */
    public function getSlaveWebsite()
    {
        $slaveWebsiteId = $this->getSlaveWebsiteId();
        if (!is_null($slaveWebsiteId)) {
            return Mage::app()->getWebsite($slaveWebsiteId);
        } else {
            return false;
        }
    }

    /**
     * Retrieve staging website staging stores
     *
     * @return Varien_Data_Collection
     */
    public function getStoresCollection()
    {
        if (is_null($this->_stores)) {
            $this->_stores = Mage::getResourceModel('enterprise_staging/staging_store_collection')
                ->addStagingWebsiteFilter($this->getId());

            if ($this->getId()) {
                foreach ($this->_stores as $store) {
                    $store->setStagingWebsite($this);
                }
            }
        }
        return $this->_stores;
    }

    /**
     * add store in store collection
     *
     * @param Enterprise_Staging_Model_Staging_Store $store
     * @return Enterprise_Staging_Model_Staging_Website
     */
    public function addStore(Enterprise_Staging_Model_Staging_Store $store)
    {
        $store->setStagingWebsite($this);
        if (!$store->getId()) {
            $this->getStoresCollection()->addItem($store);
        }
        return $this;
    }

    /**
     * get store ids 
     *
     * @return mixed
     */
    public function getStoreIds()
    {
        if ($this->hasData('store_ids')) {
            $ids = $this->getData('store_ids');
            if (!is_array($ids)) {
                $ids = !empty($ids) ? explode(',', $ids) : array();
                //$this->setData('store_ids', $ids);
                return $ids;
            }
        } else {
            $ids = array();
            foreach ($this->getStoresCollection() as $store) {
                $ids[] = $store->getId();
            }
            $this->setData('store_ids', $ids);
        }
        return $this->getData('store_ids');
    }

    /**
     * Update an attribute value
     *
     * @param string    $attribute
     * @param string    $value
     *
     * @return Enterprise_Staging_Model_Staging_Website
     */
    public function updateAttribute($attribute, $value)
    {
        return $this->getResource()->updateAttribute($this, $attribute, $value);
    }

    /**
     * init Enterprise_Staging_Model_Staging_Website object by slave website id
     *
     * @param int $id
     * @return Enterprise_Staging_Model_Staging_Website
     */
    public function loadBySlaveWebsiteId($id)
    {
        $this->getResource()->loadBySlaveWebsiteId($this, $id);
        
        return $this;
    }

    /**
     * sync website
     *
     * @param Mage_Core_Model_Website $website
     * @return Enterprise_Staging_Model_Staging_Website
     */
    public function syncWithWebsite(Mage_Core_Model_Website $website)
    {
        $this->getResource()->syncWithWebsite($this, $website);

        return $this;
    }
}