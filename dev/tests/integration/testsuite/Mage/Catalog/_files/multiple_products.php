<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $product Mage_Catalog_Model_Product */
$product = Mage::getModel('Mage_Catalog_Model_Product');

$product->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
    ->setId(10)
    ->setAttributeSetId(4)
    ->setName('Simple Product')
    ->setSku('simple1')
    ->setIsObjectNew(true)
    ->setTaxClassId('none')
    ->setDescription('description')
    ->setShortDescription('short description')
    ->setOptionsContainer('container1')
    ->setMsrpDisplayActualPriceType(
        Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type::TYPE_IN_CART
    )
    ->setPrice(10)
    ->setWeight(1)
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')

    ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
    ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
    ->setWebsiteIds(array(1))
    ->setCateroryIds(array())
    ->setStockData(
        array(
            'use_config_manage_stock'   => 1,
            'qty'                       => 100,
            'is_qty_decimal'            => 0,
            'is_in_stock'               => 1,
        )
    )

    ->save();

$product = Mage::getModel('Mage_Catalog_Model_Product');
$product->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
    ->setId(11)
    ->setAttributeSetId(4)
    ->setName('Simple Product2')
    ->setSku('simple2')
    ->setIsObjectNew()
    ->setTaxClassId('none')
    ->setDescription('description')
    ->setShortDescription('short description')
    ->setEnableGooglecheckout(false)
    ->setOptionsContainer('container1')
    ->setMsrpEnabled(
        Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type_Enabled::MSRP_ENABLE_YES
    )
    ->setMsrpDisplayActualPriceType(
        Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type::TYPE_ON_GESTURE
    )
    ->setPrice(20)
    ->setWeight(1)
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')
    ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG)
    ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
    ->setWebsiteIds(array(1))
    ->setCateroryIds(array())
    ->setStockData(
        array(
            'use_config_manage_stock'   => 1,
            'qty'                       => 50,
            'is_qty_decimal'            => 0,
            'is_in_stock'               => 1,
        )
    )

    ->save();

$product = Mage::getModel('Mage_Catalog_Model_Product');
$product->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
    ->setId(12)
    ->setAttributeSetId(4)
    ->setName('Simple Product 3')
    ->setSku('simple3')
    ->setIsObjectNew()
    ->setTaxClassId('none')
    ->setDescription('description')
    ->setShortDescription('short description')
    ->setMsrpEnabled(
        Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type_Enabled::MSRP_ENABLE_NO
    )
    ->setPrice(30)
    ->setWeight(1)
    ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG)
    ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_DISABLED)
    ->setWebsiteIds(array(1))
    ->setCateroryIds(array())
    ->setStockData(
        array(
            'use_config_manage_stock'   => 1,
            'qty'                       => 140,
            'is_qty_decimal'            => 0,
            'is_in_stock'               => 1,
        )
    )
    ->save();
