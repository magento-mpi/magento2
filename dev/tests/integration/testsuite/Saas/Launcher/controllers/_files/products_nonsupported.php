<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$statusType = array(
    Mage_Catalog_Model_Product_Status::STATUS_DISABLED => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
    Mage_Catalog_Model_Product_Status::STATUS_ENABLED => Mage_Catalog_Model_Product_Type::TYPE_BUNDLE,
    Mage_Catalog_Model_Product_Status::STATUS_ENABLED => Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
    Mage_Catalog_Model_Product_Status::STATUS_ENABLED => Mage_Catalog_Model_Product_Type::TYPE_GROUPED,
);

foreach ($statusType as $status => $type) {
    /** @var $product Mage_Catalog_Model_Product */
    $product = Mage::getModel('Mage_Catalog_Model_Product');

    $product->setTypeId($type)
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
        ->setStatus($status)
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
}
