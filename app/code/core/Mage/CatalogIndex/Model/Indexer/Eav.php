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
 * Catalog indexer eav processor
 *
 * @author Sasha Boyko <alex.boyko@varien.com>
 */
class Mage_CatalogIndex_Model_Indexer_Eav
    extends Mage_CatalogIndex_Model_Indexer_Abstract
    implements Mage_CatalogIndex_Model_Indexer_Interface
{
    protected function _construct()
    {
        $this->_init('catalogindex/indexer_eav');
        return parent::_construct();
    }

    public function createIndexData(Mage_Catalog_Model_Product $object, Mage_Eav_Model_Entity_Attribute_Abstract $attribute)
    {
        $data = array();

        $data['store_id'] = $attribute->getStoreId();
        $data['entity_id'] = $object->getId();
        $data['attribute_id'] = $attribute->getId();
        $data['value'] = $object->getData($attribute->getAttributeCode());

        return $this->_spreadDataForStores($object, $attribute, $data);
    }

    protected function _isAttributeIndexable(Mage_Catalog_Model_Product $object, Mage_Eav_Model_Entity_Attribute_Abstract $attribute)
    {
        if ($attribute->getIsFilterable() == 0) {
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
        $conditions['is_filterable'] = 1;
        return $conditions;
    }
}