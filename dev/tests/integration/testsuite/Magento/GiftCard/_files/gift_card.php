<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $product Magento_Catalog_Model_Product */
$product = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Product');
$product->setTypeId(Magento_GiftCard_Model_Catalog_Product_Type_Giftcard::TYPE_GIFTCARD)
    ->setId(1)
    ->setAttributeSetId(4)
    ->setWebsiteIds(array(1))
    ->setName('Gift Card')
    ->setSku('gift-card')
    ->setPrice(10)
    ->setDescription('Gift Card Description')
    ->setMetaTitle('Gift Card Meta Title')
    ->setMetaKeyword('Gift Card Meta Keyword')
    ->setMetaDescription('Gift Card Meta Description')
    ->setVisibility(Magento_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
    ->setStatus(Magento_Catalog_Model_Product_Status::STATUS_ENABLED)
    ->setCategoryIds(array(2))
    ->setStockData(array('use_config_manage_stock' => 0))
    ->setCanSaveCustomOptions(true)
    ->setHasOptions(true)
    ->setAllowOpenAmount(1)
    ->save();

