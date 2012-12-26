<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
$customer = Mage::getModel('Mage_Customer_Model_Customer');
$customer->setStoreId(1)
    ->setCreatedIn('Default Store View')
    ->setDefaultBilling(1)
    ->setDefaultShipping(1)
    ->setEmail('mr.apia.customer' . uniqid() . '@test.com')
    ->setFirstname('Test' . uniqid())
    ->setLastname('Test' . uniqid())
    ->setMiddlename('Test' . uniqid())
    ->setGroupId(1)
    ->setRewardUpdateNotification(1)
    ->setRewardWarningNotification(1);
return $customer;
