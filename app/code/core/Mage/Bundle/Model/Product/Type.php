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
 * @package    Mage_Bundle
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Bundle product type implementation
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Model_Product_Type extends Mage_Catalog_Model_Product_Type_Abstract
{
    protected $_isComposite = true;

    protected $_optionsCollection;
    protected $_selectionsCollection;
    protected $_storeFilter = null;

    protected $_usedProductsIds = null;
    protected $_usedProducts = null;

    /**
     * Return product price based on price_type attribute
     *
     * @return decimal
     */
    public function getPrice()
    {
        /**
         * @todo Change this to return valid price
         */
        return $this->getProduct()->getData('price');
    }

    /**
     * Return product sku based on sku_type attribute
     *
     * @return string
     */
    public function getSku()
    {
        /**
         * @todo Change this to return valid sku
         */
        return $this->getProduct()->getData('sku');
    }

    /**
     * Return product weight based on weight_type attribute
     *
     * @return decimal
     */
    public function getWeight()
    {
        /**
         * @todo Change this to return valid weight
         */
        return $this->getProduct()->getData('weight');
    }

    public function save()
    {
        parent::save();

        if ($options = $this->getProduct()->getBundleOptionsData()) {
            foreach ($options as $key => $option) {
                if (!$option['option_id']) {
                    unset($option['option_id']);
                }

                $optionModel = Mage::getModel('bundle/option')
                    ->setData($option)
                    ->setParentId($this->getProduct()->getId())
                    ->setStoreId($this->getProduct()->getStoreId());

                $optionModel->isDeleted((bool)$option['delete']);
                $optionModel->save();

                $options[$key]['option_id'] = $optionModel->getOptionId();
            }

            if ($selections = $this->getProduct()->getBundleSelectionsData()) {
                foreach ($selections as $index => $group) {
                    foreach ($group as $key => $selection) {
                        if (!$selection['selection_id']) {
                            unset($selection['selection_id']);
                        }

                        $selectionModel = Mage::getModel('bundle/selection')
                            ->setData($selection)
                            ->setOptionId($options[$index]['option_id']);

                        $selectionModel->isDeleted((bool)$selection['delete']);
                        $selectionModel->save();

                        $selection['selection_id'] = $selectionModel->getSelectionId();
                    }
                }
            }
        }

        return $this;
    }

    public function getOptions()
    {
        return $this->getOptionsCollection()->getItems();
    }

    public function getOptionsIds()
    {
        return $this->getOptionsCollection()->getAllIds();
    }

    public function getOptionsCollection()
    {
        if (!$this->_optionsCollection) {
            $this->_optionsCollection = Mage::getModel('bundle/option')->getResourceCollection()
                ->setProductIdFilter($this->getProduct()->getId())
                ->setPositionOrder()
                ->joinValues($this->getStoreFilter());
        }
        return $this->_optionsCollection;
    }
/*
    public function getUsedProductsIds()
    {
        if (!$this->_usedProductsIds) {
            $this->_usedProductsIds = $this->getOptionsCollection()->getUsedProductsIds();
        }
        return $this->_usedProductsIds;
    }
*/

    public function getSelectionsCollection($optionIds)
    {
        if (!$this->_selectionsCollection) {
            $this->_selectionsCollection = Mage::getResourceModel('bundle/selection_collection')
                ->addAttributeToSelect('*')
                ->setOptionIdsFilter($optionIds);
        }
        return $this->_selectionsCollection;
    }


    /**
     * Checking if we can sale this bundle
     *
     * @return bool
     */
    public function isSalable()
    {
        if (!parent::isSalable()) {
            return false;
        }
        return true;
        /**
         * @todo check all selection for available
         */
    }

    /**
     * Retrive store filter for associated products
     *
     * @return int|Mage_Core_Model_Store
     */
    public function getStoreFilter()
    {
        return $this->_storeFilter;
    }

    /**
     * Set store filter for associated products
     *
     * @param $store int|Mage_Core_Model_Store
     * @return Mage_Catalog_Model_Product_Type_Configurable
     */
    public function setStoreFilter($store=null) {
        $this->_storeFilter = $store;
        return $this;
    }

}
