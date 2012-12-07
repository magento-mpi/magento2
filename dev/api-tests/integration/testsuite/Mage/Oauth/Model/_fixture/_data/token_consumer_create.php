<?php
/**
 * Make fixtures for tokens, consumers
 *
 * @return array
 */
/** @var $_this Magento_TestCase */
$_this = $this;

$adminId = $_this->getDefaultAdminUser()->getId();
$customerId = $_this->getDefaultCustomer()->getId();

//data sets
$data = array(
    array(
        'token' => array(
            'authorized'  => 0,
            'revoked'     => 0,
            'type'        => Mage_Oauth_Model_Token::TYPE_REQUEST,
        )
    ),
    array(
        'token' => array(
            'authorized'  => 1,
            'revoked'     => 0,
            'type'        => Mage_Oauth_Model_Token::TYPE_ACCESS,
        )
    ),
    array(
        'token' => array(
            'authorized'  => 1,
            'revoked'     => 1,
            'type'        => Mage_Oauth_Model_Token::TYPE_ACCESS,
        )
    ),
);

$models = array();
/** @var $helper Mage_Oauth_Helper_Data */
$helper = Mage::helper('Mage_Oauth_Helper_Data');

foreach ($data as $item) {
    $consumer = new Mage_Oauth_Model_Consumer();
    $consumerData = require 'consumer_data.php';
    $consumer->setData($consumerData['create']);
    $consumer->save();
    $_this->addModelToDelete($consumer);
    $models['consumer'][] = $consumer;

    //customer
    $token = new Mage_Oauth_Model_Token();
    $tokenData = require 'token_data.php';
    $tokenData = $tokenData['create'];
    $tokenData['consumer_id'] = $consumer->getId();
    $tokenData['customer_id'] = $customerId;
    $tokenData = array_merge($tokenData, $item['token']);
    $token->setData($tokenData);
    $token->save();
    $_this->addModelToDelete($token);
    $models['token']['customer'][] = $token;

    //admin
    unset($tokenData['customer_id']);
    $token = new Mage_Oauth_Model_Token();
    $tokenData['admin_id'] = $adminId;
    $tokenData['token']    = $helper->generateToken(); //must be unique
    $tokenData = array_merge($tokenData, $item['token']);
    $token->setData($tokenData);
    $token->save();
    $_this->addModelToDelete($token);
    $models['token']['admin'][] = $token;
}

return $models;


