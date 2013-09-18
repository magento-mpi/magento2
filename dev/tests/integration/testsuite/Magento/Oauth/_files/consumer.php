<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
$consumer = Mage::getModel('Magento_Oauth_Model_Consumer');
$consumer
    ->setEntityId(1)
    ->setCreatedAt('2012-12-31 23:59:59')
    ->setUpdatedAt('2012-12-31 23:59:59')
    ->setName('consumeName')
    ->setKey('consumerKey')
    ->setSecret('consumerSecret')
    ->callbackUrl('http://magento.ll')
    ->rejectedCallbackUrl('http://magento.ll')
    ->httpPostUrl('http://magento.ll');

$consumer->isObjectNew(true);
$consumer->save();
