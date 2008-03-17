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
 * @package    Mage_CatalogIndex
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog indexer price processor
 *
 * @author Sasha Boyko <alex.boyko@varien.com>
 */
class Mage_CatalogIndex_Model_Indexer_Price
    extends Mage_CatalogIndex_Model_Indexer_Abstract
    implements Mage_CatalogIndex_Model_Indexer_Interface
{
    protected function _construct()
    {
        $this->_init('catalogindex/indexer_price');
        $this->_currencyModel = Mage::getModel('directory/currency');

        return parent::_construct();
    }

    protected function _createTierPriceIndexData(Mage_Catalog_Model_Product $object, Mage_Eav_Model_Entity_Attribute_Abstract $attribute, array $data)
    {
        $origData = $data;
        $result = array();
        foreach ($data['value'] as $row) {
            if (!$row['delete']) {
                $data['qty'] = $row['price_qty'];
                $data['customer_group_id'] = $row['cust_group'];
                $data['value'] = $row['price'];

                $data = $this->_spreadDataForStores($object, $attribute, $data, $row['website_id']);

                $result = array_merge($result, $data);
                $data = $origData;
            }
        }
        return $result;
    }

    public function createIndexData(Mage_Catalog_Model_Product $object, Mage_Eav_Model_Entity_Attribute_Abstract $attribute)
    {
        $data = array();

        $data['store_id'] = $attribute->getStoreId();
        $data['entity_id'] = $object->getId();
        $data['attribute_id'] = $attribute->getId();
        $data['customer_group_id'] = '';
        $data['qty'] = '';
        $data['value'] = $object->getData($attribute->getAttributeCode());

        if ($attribute->getAttributeCode() != 'tier_price') {
            return $this->_spreadDataForStores($object, $attribute, $data);
        } else {
            return $this->_createTierPriceIndexData($object, $attribute, $data);
        }
    }

    protected function _isAttributeIndexable(Mage_Catalog_Model_Product $object, Mage_Eav_Model_Entity_Attribute_Abstract $attribute)
    {
        if ($attribute->getFrontendInput() != 'price' && $attribute->getAttributeCode() != 'tier_price') {
            return false;
        }

        if ($object->getData($attribute->getAttributeCode()) == null) {
            return false;
        }

        return true;
    }

    protected function _getIndexableAttributeConditions()
    {
        $conditions = array();
        $conditions['frontend_input'] = array('price', 'tier_price');
        return $conditions;
    }
}