<?php
/**
 * Save quote_with_giftcard_saved fixture
 *
 * The quote is not saved inside the original fixture. It is later saved inside child fixtures, but along with some
 * additional data which may break some tests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
require 'giftcardaccount.php';

require __DIR__ . '/../../Checkout/_files/quote_with_address_saved.php';
/** @var  \Magento\GiftCardAccount\Model\Giftcardaccount $giftCardAccount */
$giftCardAccount = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\GiftCardAccount\Model\Giftcardaccount');
$giftCardAccount->loadByCode('giftcardaccount_fixture');
$giftCardAccount->addToCart(true, $quote);

