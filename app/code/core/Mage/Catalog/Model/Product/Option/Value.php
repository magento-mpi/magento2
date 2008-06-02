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
 * Catalog product option select type model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Option_Value extends Mage_Core_Model_Abstract
{
    protected $_product;

    public function __construct()
    {
        $this->_init('catalog/product_option_value');
    }

    public function setProduct($product)
    {
        $this->_product = $product;
        return $this;
    }

    public function getProduct()
    {
        return $this->_product;
    }

    /**
     * Enter description here...
     *
     * @param Mage_Catalog_Model_Product_Option $option
     */
    public function getValuesCollection($option_id, $store_id)
    {
        $collection = Mage::getResourceModel('catalog/product_option_value_collection')
            ->addFieldToFilter('option_id', $option_id)
            ->getValues($store_id);
        return $collection;
    }

    public function getValuesByOption($optionIds, $option_id, $store_id)
    {
        $collection = Mage::getResourceModel('catalog/product_option_value_collection')
            ->addFieldToFilter('option_id', $option_id)
            ->getValuesByOption($optionIds, $store_id);

        return $collection;
    }

    public function deleteValue($option_id)
    {
        $this->getResource()->deleteValue($option_id);
    }

    public function deleteValues($option_type_id)
    {
        $this->getResource()->deleteValues($option_type_id);
    }
}