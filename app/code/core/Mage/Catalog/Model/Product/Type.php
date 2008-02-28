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
 * Product type model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Product_Type
{
    /**
     * Available product types
     */
    const TYPE_SIMPLE       = 1;
    const TYPE_BUNDLE       = 2;
    const TYPE_CONFIGURABLE = 3;
    const TYPE_GROUPED      = 4;
    const TYPE_VIRTUAL      = 5;

    public static function factory($product)
    {
        switch ($product->getTypeId()) {
            case self::TYPE_CONFIGURABLE:
                $typeModel = Mage::getModel('catalog/product_type_configurable');
                break;
            case self::TYPE_GROUPED:
                $typeModel = Mage::getModel('catalog/product_type_grouped');
                break;
            default:
                $typeModel = Mage::getModel('catalog/product_type_simple');
                break;
        }
        $typeModel->setProduct($product);
        return $typeModel;
    }

    static public function getOptionArray()
    {
        return array(
            self::TYPE_SIMPLE       => Mage::helper('catalog')->__('Simple'),
            self::TYPE_GROUPED      => Mage::helper('catalog')->__('Grouped'),
            self::TYPE_CONFIGURABLE => Mage::helper('catalog')->__('Configurable'),
            //self::TYPE_BUNDLE       => Mage::helper('catalog')->__('Bundle'),
            //self::TYPE_VIRTUAL      => Mage::helper('catalog')->__('Virtual'),
        );
    }

    static public function getAllOption()
    {
        $options = self::getOptionArray();
        array_unshift($options, array('value'=>'', 'label'=>''));
        return $options;
    }

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

    static public function getOptionText($optionId)
    {
        $options = self::getOptionArray();
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }
}
