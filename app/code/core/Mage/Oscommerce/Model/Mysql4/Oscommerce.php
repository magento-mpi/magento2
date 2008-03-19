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
 * @category   Mage
 * @package    Mage_Dataflow
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Convert profile resource model
 *
 * @category   Mage
 * @package    Mage_OsCommerce
 * @author     Kyaw Soe Lynn Maung <vincent@varien.com>
 */
class Mage_Oscommerce_Model_Mysql4_Oscommerce extends Mage_Core_Model_Mysql4_Abstract
{
	const DEFAULT_WEBSITE_STORE = 1;
    protected function _construct()
    {
        $this->_init('oscommerce/oscommerce', 'import_id');
    }


    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getCreatedAt()) {
            $object->setCreatedAt($this->formatDate(time()));
        }
        $object->setUpdatedAt($this->formatDate(time()));
        parent::_beforeSave($object);
    }

    protected function _getForeignAdapter()
    {
        return $this->_getConnection('foreign');
    }
    
    public function debug()
    {
        $res = $this->_getForeignAdapter();
        var_dump($res);
    }
    
    public function getProducts()
    {
    	$connection = $this->_getForeignAdapter();
    	$select = $connection->select()->from('products', array('*'));
    	$result = $connection->fetchAll($select);
    }    
    
    public function getStores()
    {
    	$select = $this->_getForeignAdapter()->select();
    	$select = "select languages_id id, name, directory code, 1 is_active from languages";
    	if (!($result = $this->_getForeignAdapter()->fetchAll($select))) {
    		$result = array();
    	}
    	return $result;
    }
    
    public function importStores(Mage_Oscommerce_Model_Oscommerce $obj)
    {
//    	if (!($container = Mage::registry('osc_import_container'))) {
//    		$container = array();
//    	}
    	$groupmodel = mage::getmodel('core/store_group')->load(self::DEFAULT_WEBSITE_STORE);
    	$log['import_id'] = $obj->getId();
    	$log['type_id'] = $this->getImportTypeIdByCode('store');
    	$log['user_id'] = Mage::getSingleton('admin/session')->getUser()->getId();
    	if ($stores = $this->getStores()) foreach($stores as $store) {
			try {
		    	$log['value'] = $store['id'];
		    	unset($store['id']);
		    	$store['group_id'] = self::DEFAULT_WEBSITE_STORE;
		    	$storemodel = mage::getmodel('core/store')->setdata($store);
		    	$storemodel->setId(null);
		    	$storemodel->setwebsiteid($groupmodel->getwebsiteid());
		    	$storemodel->save();
		    	$log['ref_id'] = $storemodel->getId();
		    	$log['created_at'] = $this->formatDate(time());
		    	$this->log($log);
			} catch (Exception $e) {
			}
    	}
    	unset($stores);
    }
    
    public function getCategories()
    {
    	
    }
    
    public function importCategories(Mage_Oscommerce_Model_Oscommerce $obj)
    {
		    		
    }
    
    /**
     * Insert log 
     *
     * @param array data
     */
    public function log($data) 
    {
    	if (isset($data)) {
    		$this->_getWriteAdapter()->beginTransaction();
    		try {
    			$this->_getWriteAdapter()->insert($this->getTable('oscommerce_ref'), $data);
    			$this->_getWriteAdapter()->commit();
    		} catch (Exception $e) {
    			$this->_getWriteAdapter()->rollBack();
    		}
    	}
    }
    
    public function getImportTypes()
    {
    	$connection = $this->_getReadAdapter();
    	$select = $connection->select();
    	$select->from($this->getTable('oscommerce_type'), array('*'));
    	if (!($result = $connection->fetchAll($select))) {
    		$result = array();
    	}
    	return $result;
    }
    
    public function getImportTypeIdByCode($code = '') {
    	$types = $this->getImportTypes();
    	if (isset($code) && $types) foreach ($types as $type) {
    		if ($type['type_code'] == $code) {
    			return $type['type_id'];
    		}
    	}
    	return false;
    }
    
}