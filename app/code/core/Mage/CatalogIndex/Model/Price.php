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
 * @category    Mage
 * @package     Mage_CatalogIndex
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Price index model
 *
 * @method Mage_CatalogIndex_Model_Resource_Price _getResource()
 * @method Mage_CatalogIndex_Model_Resource_Price getResource()
 * @method int setEntityId(int $value)
 * @method Mage_CatalogIndex_Model_Price getCustomerGroupId()
 * @method int setCustomerGroupId(int $value)
 * @method Mage_CatalogIndex_Model_Price getWebsiteId()
 * @method int setWebsiteId(int $value)
 * @method Mage_CatalogIndex_Model_Price getTaxClassId()
 * @method int setTaxClassId(int $value)
 * @method Mage_CatalogIndex_Model_Price getPrice()
 * @method float setPrice(float $value)
 * @method Mage_CatalogIndex_Model_Price getFinalPrice()
 * @method float setFinalPrice(float $value)
 * @method Mage_CatalogIndex_Model_Price getMinPrice()
 * @method float setMinPrice(float $value)
 * @method Mage_CatalogIndex_Model_Price getMaxPrice()
 * @method float setMaxPrice(float $value)
 * @method Mage_CatalogIndex_Model_Price getTierPrice()
 * @method float setTierPrice(float $value)
 *
 * @category    Mage
 * @package     Mage_CatalogIndex
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogIndex_Model_Price extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('catalogindex/price');
        $this->_getResource()->setStoreId(Mage::app()->getStore()->getId());
        $this->_getResource()->setRate(Mage::app()->getStore()->getCurrentCurrencyRate());
        $this->_getResource()->setCustomerGroupId(Mage::getSingleton('customer/session')->getCustomerGroupId());
    }

    public function getMaxValue($attribute, $entityIdsFilter)
    {
        return $this->_getResource()->getMaxValue($attribute, $entityIdsFilter);
    }

    public function getCount($attribute, $range, $entitySelect)
    {
        return $this->_getResource()->getCount($range, $attribute, $entitySelect);
    }

    public function getFilteredEntities($attribute, $range, $index, $entityIdsFilter)
    {
        return $this->_getResource()->getFilteredEntities($range, $index, $attribute, $entityIdsFilter);
    }

    public function applyFilterToCollection($collection, $attribute, $range, $index)
    {
        return $this->_getResource()->applyFilterToCollection($collection, $attribute, $range, $index);
    }

    public function addMinimalPrices(Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection)
    {
        $minimalPrices = $this->_getResource()->getMinimalPrices($collection->getLoadedIds());

        foreach ($minimalPrices as $row) {
            $item = $collection->getItemById($row['entity_id']);
            if ($item) {
                $item->setData('minimal_price', $row['value']);
                $item->setData('minimal_tax_class_id', $row['tax_class_id']);
            }
        }
    }
}
