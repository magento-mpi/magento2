<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
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
    ->setName('GiftCard Product')
    ->setSku('gift1')
    ->setPrice(10)
    ->setDescription('Description with <b>html tag</b>')
    ->setMetaTitle('gift meta title')
    ->setMetaKeyword('gift meta keyword')
    ->setMetaDescription('gift meta description')
    ->setVisibility(Magento_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
    ->setStatus(Magento_Catalog_Model_Product_Status::STATUS_ENABLED)
    ->setCategoryIds(array(2))
    ->setStockData(
        array(
            'use_config_manage_stock'   => 0,
        )
    )
    ->setCanSaveCustomOptions(true)
    ->setHasOptions(true)
    ->setAllowOpenAmount(1)
    ->save();

/** @var $product Magento_Catalog_Model_Product */
$product = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Product');
$product->load(1);

$requestInfo = new Magento_Object(array(
    'qty' => 1,
    'giftcard_amount'         => 'custom',
    'custom_giftcard_amount'  => 200,
    'giftcard_sender_name'    => 'Sender',
    'giftcard_sender_email'   => 'aerfg@sergserg.com',
    'giftcard_recipient_name' => 'Recipient',
    'giftcard_recipient_email'=> 'awefaef@dsrthb.com',
    'giftcard_message'        => 'message'
));

require __DIR__ . '/../../../Magento/Checkout/_files/cart.php';
