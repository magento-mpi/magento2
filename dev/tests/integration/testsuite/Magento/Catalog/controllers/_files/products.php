<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

// Copy images to tmp media path
/** @var Magento_Catalog_Model_Product_Media_Config $config */
$config = Mage::getSingleton('Magento_Catalog_Model_Product_Media_Config');
$baseTmpMediaPath = $config->getBaseTmpMediaPath();

/** @var Magento_Filesystem $filesystem */
$filesystem = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create('Magento_Filesystem');
$filesystem->setIsAllowCreateDirectories(true);
$filesystem->copy(__DIR__ . '/product_image.png', $baseTmpMediaPath . '/product_image.png');

/** @var $productOne Magento_Catalog_Model_Product */
$productOne = Mage::getModel('Magento_Catalog_Model_Product');
$productOne->setId(1)
    ->setTypeId(Magento_Catalog_Model_Product_Type::TYPE_SIMPLE)
    ->setAttributeSetId(4)
    ->setWebsiteIds(array(Mage::app()->getStore()->getWebsiteId()))

    ->setSku('simple_product_1')
    ->setName('Simple Product 1 Name')
    ->setDescription('Simple Product 1 Full Description')
    ->setShortDescription('Simple Product 1 Short Description')

    ->setPrice(1234.56)
    ->setTaxClassId(2)
    ->setStockData(array(
        'use_config_manage_stock'   => 1,
        'qty'                       => 99,
        'is_in_stock'               => 1,
    ))

    ->setMetaTitle('Simple Product 1 Meta Title')
    ->setMetaKeyword('Simple Product 1 Meta Keyword')
    ->setMetaDescription('Simple Product 1 Meta Description')

    ->setVisibility(Magento_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
    ->setStatus(Magento_Catalog_Model_Product_Status::STATUS_ENABLED)

    ->addImageToMediaGallery($baseTmpMediaPath . '/product_image.png', null, false, false)

    ->save();

/** @var $productTwo Magento_Catalog_Model_Product */
$productTwo = Mage::getModel('Magento_Catalog_Model_Product');
$productTwo->setId(2)
    ->setTypeId(Magento_Catalog_Model_Product_Type::TYPE_SIMPLE)
    ->setAttributeSetId(4)
    ->setWebsiteIds(array(Mage::app()->getStore()->getWebsiteId()))

    ->setSku('simple_product_2')
    ->setName('Simple Product 2 Name')
    ->setDescription('Simple Product 2 Full Description')
    ->setShortDescription('Simple Product 2 Short Description')

    ->setPrice(987.65)
    ->setTaxClassId(2)
    ->setStockData(array(
        'use_config_manage_stock'   => 1,
        'qty'                       => 24,
        'is_in_stock'               => 1,
    ))

    ->setVisibility(Magento_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
    ->setStatus(Magento_Catalog_Model_Product_Status::STATUS_ENABLED)

    ->save();
