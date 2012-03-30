<?php

/** @var $consumer Mage_Oauth_Model_Consumer */
$consumer = require 'consumer.php';

/**
 * Model data
 *
 * @return array
 */
/** @var $helper Mage_Oauth_Helper_Data */
$helper = Mage::helper('oauth');
$date = date('Ymd-His');
$tokenData = array(
    'consumer_id'       => $consumer->getId(),
    'admin_id'          => null,
    'customer_id'       => null,
    'type'              => Mage_Oauth_Model_Token::TYPE_REQUEST,
    'token'             => $helper->generateToken(),
    'secret'            => $helper->generateTokenSecret(),
    'verifier'          => null,  //$helper->generateVerifier(),
    'callback_url'      => 'http://example.com/oauth_model/?oauthSecret=secret&d=' . $date,
    'authorized'        => '0',
    'revoked'           => '0',
);

$token = new Mage_Oauth_Model_Token();
$token->setData($tokenData);
$token->save();

return array($token, $consumer);
