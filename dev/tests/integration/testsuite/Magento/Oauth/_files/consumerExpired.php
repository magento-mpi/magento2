<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$url = 'http://magento.ll';

/** @var $objectManager Magento_TestFramework_ObjectManager */
$objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();

/** @var $consumer Magento_Oauth_Model_Consumer */
$consumer = $objectManager->create('Magento_Oauth_Model_Consumer');
$consumer
    ->setCreatedAt('2012-12-31 23:59:59')
    ->setUpdatedAt('2012-12-31 23:59:59')
    ->setName('consumerName')
    ->setKey(Magento_Webapi_Authentication_RestTest::CONSUMER_KEY)
    ->setSecret(Magento_Webapi_Authentication_RestTest::CONSUMER_SECRET)
    ->setCallbackUrl($url)
    ->setRejectedCallbackUrl($url)
    ->setHttpPostUrl($url);

$consumer->isObjectNew(true);
$consumer->save();

/** @var  $token Magento_Oauth_Model_Token */
$token = $objectManager->create('Magento_Oauth_Model_Token');
$token->createVerifierToken($consumer->getId(), $url);

