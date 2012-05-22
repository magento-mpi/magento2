<?php
/**
 * Model data
 *
 * @return array
 */
/** @var $helper Mage_Oauth_Helper_Data */
$helper = Mage::helper('Mage_Oauth_Helper_Data');
$date = date('Ymd-His');
return array(
    'create' => array(
        'consumer_id'       => '',
        'admin_id'          => null,
        'customer_id'       => null,
        'type'              => Mage_Oauth_Model_Token::TYPE_REQUEST,
        'token'             => $helper->generateToken(),
        'secret'            => $helper->generateTokenSecret(),
        'verifier'          => $helper->generateVerifier(),
        'callback_url'      => 'http://example.com/oauth_model/?oauthSecret=secret&d=' . $date,
        'authorized'        => '0',
        'revoked'           => (string) rand(0, 1),
    ),
    'update' => array(
        'type'              => Mage_Oauth_Model_Token::TYPE_ACCESS,
        'token'             => $helper->generateToken(),
        'secret'            => $helper->generateTokenSecret(),
        'verifier'          => null,
        'callback_url'      => 'http://example.com/oauth_model/?oauthKey=secret&d=' . $date,
        'authorized'        => '1',
        'revoked'           => (string) rand(0, 1),
    ),
);
