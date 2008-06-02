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
 * Catalog product option model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Option extends Mage_Core_Model_Abstract
{
    const OPTION_GROUP_TEXT   = 'text';
    const OPTION_GROUP_FILE   = 'file';
    const OPTION_GROUP_SELECT = 'select';

    const OPTION_TYPE_FIELD     = 'field';
    const OPTION_TYPE_AREA      = 'area';
    const OPTION_TYPE_FILE      = 'file';
    const OPTION_TYPE_DROP_DOWN = 'drop_down';
    const OPTION_TYPE_RADIO     = 'radio';
    const OPTION_TYPE_CHECKBOX  = 'checkbox';
    const OPTION_TYPE_MULTIPLE  = 'multiple';

    protected $_values = array();

    protected $_productId = 0;

    protected $_product;

    public function __construct()
    {
        $this->_init('catalog/product_option');
    }

    public function setProductId($productId)
    {
        $this->_productId = $productId;
        return $this;
    }

    public function getProductId()
    {
        return $this->_productId;
    }

    public function getProduct()
    {
        return $this->_product;
    }

    public function setProduct($product)
    {
        $this->_product = $product;
        return $this;
    }

    public function getGroupByType($type)
    {
        $optionGroupsToTypes = array(
            self::OPTION_TYPE_FIELD => self::OPTION_GROUP_TEXT,
            self::OPTION_TYPE_AREA => self::OPTION_GROUP_TEXT,
            self::OPTION_TYPE_FILE => self::OPTION_GROUP_FILE,
            self::OPTION_TYPE_DROP_DOWN => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_RADIO => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_CHECKBOX => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_MULTIPLE => self::OPTION_GROUP_SELECT,
        );

        return isset($optionGroupsToTypes[$type])?$optionGroupsToTypes[$type]:'';
    }

    /**
     * Enter description here...
     *
     * @param array $option
     * @return unknown
     */
    public function saveOption($option, $store_id)
    {
        $optionTypes = array(
            self::OPTION_TYPE_FIELD => self::OPTION_GROUP_TEXT,
            self::OPTION_TYPE_AREA => self::OPTION_GROUP_TEXT,
            self::OPTION_TYPE_FILE => self::OPTION_GROUP_FILE,
            self::OPTION_TYPE_DROP_DOWN => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_RADIO => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_CHECKBOX => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_MULTIPLE => self::OPTION_GROUP_SELECT,
        );

        $this->setData($option)->setData('product_id', $this->getProductId())
            ->setData('store_id', $store_id);

        if ($this->getData('option_id') == '0') {
            $this->unsetData('option_id');
        } else {
            $this->setId($this->getData('option_id'));
        }
        if ($this->getData('is_delete') == '1') {
            if ($this->getId()) {
                $valueModel = Mage::getSingleton('catalog/product_option_value')
                     ->deleteValue($this->getId());
                $this->deletePrices($this->getId());
                $this->deleteTitles($this->getId());
                $this->delete();
            }
        } else {
            if ($this->getData('previous_type') != '') {
                $previousType = $this->getData('previous_type');
                if ($optionTypes[$previousType] != $optionTypes[$this->getData('type')]) {
                    if ($previousType == self::OPTION_TYPE_DROP_DOWN
                        || $previousType == self::OPTION_TYPE_RADIO
                        || $previousType == self::OPTION_TYPE_CHECKBOX
                        || $previousType == self::OPTION_TYPE_MULTIPLE ) {

                        $this->unsetData('values');
                        if ($this->getId()) {
                            $valueModel = Mage::getSingleton('catalog/product_option_value')
                                ->deleteValue($this->getId());
                        }
                    } else {
                        if ($previousType == self::OPTION_TYPE_FIELD || $previousType == self::OPTION_TYPE_AREA) {
                            $this->setData('max_characters', '0');
                        } elseif ($previousType == self::OPTION_TYPE_FILE) {
                            $this->setData('file_extension', '');
                        }
                        $this->setData('sku', '');
                        $this->unsetData('price');
                        $this->unsetData('price_type');
                        if ($this->getId()) {
                            $this->deletePrices($this->getId());
                        }
                    }
                }
            }
            $this->save();
        }

        return $this;
    }

    public function deletePrices($option_id)
    {
        $this->getResource()->deletePrices($option_id);
    }

    public function deleteTitles($option_id)
    {
        $this->getResource()->deleteTitles($option_id);
    }

    protected function _afterSave()
    {
        if (is_array($this->getData('values'))) {
            $valueModel = Mage::getSingleton('catalog/product_option_value')
                ->setProduct($this->getProduct());
            foreach ($this->getData('values') as $value) {
                $valueModel->setData($value)->setData('option_id', $this->getId())
                    ->setData('store_id', $this->getData('store_id'));

                if ($valueModel->getData('option_type_id') == '-1') {
                    $valueModel->unsetData('option_type_id');
                } else {
                    $valueModel->setId($valueModel->getData('option_type_id'));
                }

                if ($valueModel->getData('is_delete') == '1') {
                    if ($valueModel->getId()) {
                        $valueModel->deleteValue($valueModel->getId());
                        $valueModel->delete();
                    }
                } else {
                    $valueModel->save();
                }
            }
        }

        return parent::_afterSave();
    }

    public function getProductOptionCollection($product_id, $store_id)
    {
        $collection = Mage::getResourceModel('catalog/product_option_collection')
            ->addFieldToFilter('product_id', $product_id)
            ->getOptions($store_id);

        return $collection;
    }


    public function getOptionValuesCollection($store_id)
    {
        $collection = Mage::getModel('catalog/product_option_value')
            ->getValuesCollection($this->getId(), $store_id);

        return $collection;
    }

    public function getOptionValuesByOptionId($optionIds, $store_id)
    {
//        $optionIds = '2345';
        $collection = Mage::getModel('catalog/product_option_value')
            ->getValuesByOption($optionIds, $this->getId(), $store_id);

        return $collection;
    }

}