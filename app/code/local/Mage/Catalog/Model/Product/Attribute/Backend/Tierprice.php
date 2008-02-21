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


/**
 * Catalog product tier price backend attribute model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Catalog_Model_Product_Attribute_Backend_Tierprice extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Retrieve resource model
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Backend_Tierprice
     */
    protected function _getResource()
    {
        return Mage::getResourceSingleton('catalog/product_attribute_backend_tierprice');
    }

    /**
     * Validate data
     *
     * @param   Mage_Catalog_Model_Product $object
     * @return  this
     */
    public function validate($object)
    {
        $tiers = $object->getData($this->getAttribute()->getName());
        if (empty($tiers)) {
            return $this;
        }
        $dup = array();
        foreach ($tiers as $tier) {
            if (!empty($tier['delete'])) {
                continue;
            }
            $key = $tier['cust_group'].'-'.$tier['price_qty'];
            if (!empty($dup[$key])) {
                Mage::throwException(
                    Mage::helper('catalog')->__('Duplicate tier price customer group and quantity.')
                );
            }
            $dup[$key] = 1;
        }
        return $this;
    }

    public function afterLoad($object)
    {
        $data = $this->_getResource()->loadProductPrices($object);

        foreach ($data as $i=>$row) {
            if (!empty($row['all_groups'])) {
                $data[$i]['cust_group'] = Mage_Customer_Model_Group::CUST_GROUP_ALL;
            }
        }
        $object->setData($this->getAttribute()->getName(), $data);
    }

    public function afterSave($object)
    {
        $this->_getResource()->deleteProductPrices($object);
        $tierPrices = $object->getData($this->getAttribute()->getName());

        if (!is_array($tierPrices)) {
            return $this;
        }
        //$minimalPrice = $object->getPrice();

        foreach ($tierPrices as $tierPrice) {
            if (empty($tierPrice['price_qty'])
                || !isset($tierPrice['price'])
                || !empty($tierPrice['delete'])) {
                continue;
            }

            $useForAllGroups = $tierPrice['cust_group'] == Mage_Customer_Model_Group::CUST_GROUP_ALL;

            $data = array();
            $data['all_groups']        = $useForAllGroups;
            $data['customer_group_id'] = !$useForAllGroups ? $tierPrice['cust_group'] : 0;
            $data['qty']               = $tierPrice['price_qty'];
            $data['value']             = $tierPrice['price'];

/*            if ($tierPrice['price']<$minimalPrice) {
                $minimalPrice = $tierPrice['price'];
            }*/

            $this->_getResource()->insertProductPrice($object, $data);
        }
        return $this;
        /*$object->setMinimalPrice($minimalPrice);
        $this->getAttribute()->getEntity()->saveAttribute($object, 'minimal_price');*/
    }

    public function afterDelete($object)
    {
        $this->_getResource()->deleteProductPrices($object);
        return $this;
    }
}