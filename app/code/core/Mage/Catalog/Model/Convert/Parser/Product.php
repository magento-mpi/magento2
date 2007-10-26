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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Catalog_Model_Convert_Parser_Product extends Mage_Eav_Model_Convert_Parser_Abstract
{
    /**
     * Product collections per store
     *
     * @var array
     */
    protected $_collections;

    /**
     * @return Mage_Catalog_Model_Mysql4_Convert
     */
    public function getResource()
    {
        if (!$this->_resource) {
            $this->_resource = Mage::getResourceSingleton('catalog_entity/convert')
                ->loadStores()
                ->loadProducts()
                ->loadAttributeSets()
                ->loadAttributeOptions();
        }
        return $this->_resource;
    }

    public function getCollection($storeId)
    {
        if (!isset($this->_collections[$storeId])) {
            $this->_collections[$storeId] = Mage::getResourceModel('catalog/product_collection');
            $this->_collections[$storeId]->getEntity()->setStoreId($storeId);
        }
        return $this->_collections[$storeId];
    }

    public function parse()
    {
        $data = $this->getData();

        $collection = Mage::getResourceModel('catalog/product_collection');
        $entity = $collection->getEntity();

        $result = array();
        foreach ($data as $i=>$row) {
            // validate SKU
            if (empty($row['sku'])) {
                $this->addException(__('Missing SKU, skipping the record'), Varien_Convert_Exception::ERROR, 'Line: '.$i);
                continue;
            }

            // get store ids
            if (empty($row['store'])) {
                $storeIds = array(0);
            } else {
                $storeIds = array();
                foreach (explode(',', $row['store']) as $store) {
                    $storeNode = Mage::getConfig()->getNode('stores/'.$store);
                    if (!$storeNode) {
                        $this->addException(__("Invalid store specified, skipping the record"),
                            Varien_Convert_Exception::ERROR, 'Line: '.$i.', sku: '.$row['sku']);
                        continue;
                    }
                    $storeIds[] = (int)$storeNode->system->store->id;
                }
            }

            // get attribute set id
            if (empty($row['attribute_set'])) {
                $row['attribute_set'] = 'Default';
            }
            if (!$this->getResource()->getAttributeSet($row['attribute_set'])) {
                $this->addException(__("Invalid attribute set specified, skipping the record"),
                    Varien_Convert_Exception::ERROR, 'Line: '.$i.', sku: '.$row['sku']);
                    continue;
            }

            // import data
            foreach ($storeIds as $storeId) {
                $collection = $this->getCollection($store);
                $entity = $collection->getEntity();

                if (empty($row['entity_id'])) {
                    $row['entity_id'] = $this->getResource()->getProductIdBySku($row['sku']);
                }
                $model = Mage::getModel('catalog/product');
                foreach ($row as $field=>$value) {
                    /*
                    $source = $entity->getAttribute($field)->getSource();
                    if ($source) {
                        foreach ($source->getAllOptions() as
                    }
                    */
                    $model->setData($field, $value);
                }
                $collection->addItem($model);
            }
        }

        $this->setData($collection);
        return $this;
    }

    public function unparse()
    {
        $data = $this->getData();

        $this->setData($result);
        return $this;
    }
}