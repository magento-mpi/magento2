<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Staging mapper model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Model_Staging_Mapper
{
    /**
     * Available staging types
     */
    const TYPE_SIMPLE       = 'simple';
    const TYPE_BUNDLE       = 'bundle';
    const TYPE_CONFIGURABLE = 'configurable';
    const TYPE_GROUPED      = 'grouped';
    const TYPE_VIRTUAL      = 'virtual';
    const TYPE_GIFTCARD     = 'giftcard';

    const DEFAULT_TYPE      = 'simple';
    const DEFAULT_TYPE_MODEL    = 'catalog/product_type_simple';
    const DEFAULT_PRICE_MODEL   = 'catalog/product_type_price';

    /**
     * product type array
     *
     * @var mixed
     */
    static protected $_types;
    
    /**
     * composite product type array
     *
     * @var mixed
     */
    static protected $_compositeTypes;
    
    /**
     * Price model collection
     *
     * @var collection
     */
    static protected $_priceModels;

    /**
     * Staging type mapper instance factory
     *
     * @param   Enterprise_Staging_Model_Staging $staging
     * @param   bool $singleton
     * @return  Enterprise_Staging_Model_Staging_Mapper_Abstract
     */
    public static function factory($staging, $singleton = false)
    {
        $types = Enterprise_Staging_Model_Staging_Type::getTypes();

        if (!empty($types[$product->getTypeId()]['model'])) {
            $typeModelName = $types[$product->getTypeId()]['model'];
        } else {
            $typeModelName = self::DEFAULT_TYPE_MODEL;
        }

        if ($singleton === true) {
            $typeModel = Mage::getSingleton($typeModelName);
        }
        else {
            $typeModel = Mage::getModel($typeModelName);
            $typeModel->setProduct($product);
        }
        $typeModel->setConfig($types[$product->getTypeId()]);
        return $typeModel;
    }

    /**
     * Product type price model factory
     *
     * @param   string $productType
     * @return  Mage_Catalog_Model_Product_Type_Price
     */
    public static function priceFactory($productType)
    {
        if (isset(self::$_priceModels[$productType])) {
            return self::$_priceModels[$productType];
        }

        $types = self::getTypes();

        if (!empty($types[$productType]['price_model'])) {
            $priceModelName = $types[$productType]['price_model'];
        } else {
            $priceModelName = self::DEFAULT_PRICE_MODEL;
        }

        self::$_priceModels[$productType] = Mage::getModel($priceModelName);
        return self::$_priceModels[$productType];
    }

    /**
     * return type as option array
     *
     * @return mixed
     */
    static public function getOptionArray()
    {
        $options = array();
        foreach(self::getTypes() as $typeId=>$type) {
            $options[$typeId] = $type['label'];
        }

        return $options;
    }

    /**
     * get product type as option array with empty first element
     *
     * @return mixed
     */
    static public function getAllOption()
    {
        $options = self::getOptionArray();
        array_unshift($options, array('value'=>'', 'label'=>''));
        return $options;
    }

    /**
     * get product type as array with option attributes: value, label
     * first element is empty
     *
     * @return mixed
     */
    static public function getAllOptions()
    {
        $res = array();
        $res[] = array('value'=>'', 'label'=>'');
        foreach (self::getOptionArray() as $index => $value) {
            $res[] = array(
               'value' => $index,
               'label' => $value
            );
        }
        return $res;
    }

    /**
     * get product type as array with option attributes: value, label
     *
     * @return mixed
     */
    static public function getOptions()
    {
        $res = array();
        foreach (self::getOptionArray() as $index => $value) {
            $res[] = array(
               'value' => $index,
               'label' => $value
            );
        }
        return $res;
    }

    /**
     * get product type element as text
     *
     * @param int $optionId
     * @return string
     */
    static public function getOptionText($optionId)
    {
        $options = self::getOptionArray();
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }

    /**
     * get product type, if it is not set
     *
     * @return mixed
     */
    static public function getTypes()
    {
        if (is_null(self::$_types)) {
            self::$_types = Mage::getConfig()->getNode('global/catalog/product/type')->asArray();
        }

        return self::$_types;
    }

    /**
     * Return composite product type Ids
     *
     * @return array
     */
    static public function getCompositeTypes()
    {
        if (is_null(self::$_compositeTypes)) {
            self::$_compositeTypes = array();
            $types = self::getTypes();
            foreach ($types as $typeId=>$typeInfo) {
                if (array_key_exists('composite', $typeInfo) && $typeInfo['composite']) {
                    self::$_compositeTypes[] = $typeId;
                }
            }
        }
        return self::$_compositeTypes;
    }
}
