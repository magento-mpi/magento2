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


class Mage_Eav_Model_Convert_Adapter_Collection extends Varien_Convert_Adapter_Abstract
{
    public function getStoreId()
    {
        $store = $this->getVar('store');
        if (!$store || !Mage::getConfig()->getNode('stores/'.$store)) {
            $this->addException(__('Invalid store specified'), Varien_Convert_Exception::FATAL);
        }
        return (int)Mage::getConfig()->getNode('stores/'.$store.'/system/store/id');
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
            $collection
                ->addAttributeToSelect('*')
                ->load();
            $this->addException(__('Loaded '.$collection->getSize().' records'));
        } catch (Exception $e) {
Mage::printException($e);
            if (!$e instanceof Varien_Convert_Exception) {
                $this->addException(__('Problem loading the collection, aborting. Error: %s', $e->getMessage()),
                    Varien_Convert_Exception::FATAL);
            }
            return $this;
        }
        $this->setData($collection);
        return $this;
    }

    public function save()
    {
        $collections = $this->getData();
        if ($collections instanceof Mage_Eav_Model_Entity_Collection_Abstract) {
            $collections = array($collections->getEntity()->getStoreId()=>$collections);
        } elseif (!is_array($collections)) {
            $this->addException(__('Array of entity collections expected'), Varien_Convert_Exception::FATAL);
        }

        foreach ($collections as $storeId=>$collection) {
            $this->addException(__('"'.$collection->getEntity()->getStore()->getCode().'" store found'));

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
            } catch (Exception $e) {
                if (!$e instanceof Varien_Convert_Exception) {
                    $this->addException(__('Problem saving the collection, aborting. Error: %s', $e->getMessage()),
                        Varien_Convert_Exception::FATAL);
                }
            }
        }
        return $this;
    }
}