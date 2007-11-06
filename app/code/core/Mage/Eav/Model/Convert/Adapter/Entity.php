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
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Eav_Model_Convert_Adapter_Entity extends Varien_Convert_Adapter_Abstract
{
	protected $_filter;
	protected $_attrToDb;
	
    public function getStoreId()
    {
        $store = $this->getVar('store');
        if (is_numeric($store)) {
            return $store;
        }
        if (!$store || !Mage::getConfig()->getNode('stores/'.$store)) {
            $this->addException(__('Invalid store specified'), Varien_Convert_Exception::FATAL);
        }
        return (int)Mage::getConfig()->getNode('stores/'.$store.'/system/store/id');
    }
    /**
     * @param $attrFilter - $attrArray['attrDB']   = ['like','eq','fromTo','dateFromTo]
     * @param $attrToDb	- attribute name to DB field		
     * @return Mage_Eav_Model_Convert_Adapter_Entity
    */
    public function setFilter($attrFilterArray,$attrToDb=null){
    	$this->_filter = $attrFilterArray;
    	$this->_attrToDb=$attrToDb;
    	return $this;
    }
    
    public function load()
    {
    	if (!($entityType = $this->getVar('entity_type'))
            || !(Mage::getResourceSingleton($entityType) instanceof Mage_Eav_Model_Entity_Interface)) {
            $this->addException(__('Invalid entity specified'), Varien_Convert_Exception::FATAL);
        }
        try {
            $collection = Mage::getResourceModel($entityType.'_collection');
            $collection->getEntity()
                ->setStore($this->getStoreId());
            $var_filters = $this->getVars();
            $filters = array();
            foreach ($var_filters as $key=>$val) {
            	if(substr($key,0,6)==='filter'){
            		$keys = explode('/',$key);
            		if(isset($keys[2])){
            			if(!isset($filters[$keys[1]])){
            				$filters[$keys[1]] = array();
            			}
            			$filters[$keys[1]][$keys[2]] = $val;
            		} else {
            			$filters[$keys[1]] = $val;
            		}
            	}
            }
            $filterQuery = array();
            foreach ($filters as $key=>$val){
                if(isset($this->_filter[$key])){
                    $keyDB = (isset($this->_attrToDb[$key])) ? $this->_attrToDb[$key] : $key;
                    switch ($this->_filter[$key]){
                        case 'eq':
                            $filterQuery[] = array('attribute'=>$keyDB,'eq'=>$val);
                            break;
                        case 'like':
                            $filterQuery[] = array('attribute'=>$keyDB,'like'=>'%'.$val.'%');
                            break;
                        case 'fromTo':
                            $filterQuery[] = array('attribute'=>$keyDB,'from'=>$val['from'],'to'=>$val['to']);
                            break;
                        case 'dateFromTo':
                            $filterQuery[] = array('attribute'=>$keyDB,'from'=>$val['from'],'to'=>$val['to'],'date'=>true);
                            break;
                        default:
                        break;
                    }
                }
            }
           	if(count($filterQuery)==0){
           		$filterQuery = null;
           	}
            $collection
                ->addAttributeToSelect('*')
                ->addAttributeToFilter($filterQuery,null,'AND')
                ->load();
            $this->addException(__('Loaded '.$collection->getSize().' records'));
        } catch (Varien_Convert_Exception $e) {
            throw $e;
        } catch (Exception $e) {
            $this->addException(__('Problem loading the collection, aborting. Error: %s', $e->getMessage()),
                Varien_Convert_Exception::FATAL);
        }
        $this->setData($collection);
        return $this;
    }

    public function save()
    {
        $collection = $this->getData();
        if ($collection instanceof Mage_Eav_Model_Entity_Collection_Abstract) {
            $this->addException(__('Entity collections expected'), Varien_Convert_Exception::FATAL);
        }

        $this->addException($collection->getSize().' records found.');

        if (!$collection instanceof Mage_Eav_Model_Entity_Collection_Abstract) {
            $this->addException(__('Entity collection expected'), Varien_Convert_Exception::FATAL);
        }
        try {
            $i = 0;
            foreach ($collection->getIterator() as $model) {
                $model->save();
                $i++;
            }
            $this->addException(__("Saved ".$i." record(s)"));
        } catch (Varien_Convert_Exception $e) {
            throw $e;
        } catch (Exception $e) {
            $this->addException(__('Problem saving the collection, aborting. Error: %s', $e->getMessage()),
                Varien_Convert_Exception::FATAL);
        }
        return $this;
    }
}