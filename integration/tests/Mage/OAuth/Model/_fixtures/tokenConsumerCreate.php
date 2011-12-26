<?php
/**
 * Make fixtures for tokens, consumers
 *
 * @return array
 */
/** @var $_this Magento_TestCase */
$_this = $this;

$adminId = $_this->getDefaultAdminUser()->getId();

$addedToAdmin = 0;

//1) crate consumer-token
$consumer = new Mage_OAuth_Model_Consumer();
$data = require 'consumerData.php';
$consumer->setData($data['create']);
$consumer->save();
$_this->addModelToDelete($consumer);
$models['consumer'][] = $consumer;

$token = new Mage_OAuth_Model_Token();
$data = require 'tokenData.php';
$tokenData = $data['create'];
$tokenData['is_revoked']  = 1;
$tokenData['consumer_id'] = $consumer->getId();
$token->setData($tokenData);
$token->save();
$_this->addModelToDelete($token);
$models['token'][] = $token;

//2) crate consumer-token
$consumerData = null;
$consumer = new Mage_OAuth_Model_Consumer();
$data = require 'consumerData.php';
$consumer->setData($data['create']);
$consumer->save();
$_this->addModelToDelete($consumer);
$models['consumer'][] = $consumer;

$tokenData = null;
$token = new Mage_OAuth_Model_Token();
$data = require 'tokenData.php';
$tokenData = $data['create'];
$tokenData['is_revoked']  = 1;
$tokenData['consumer_id'] = $consumer->getId();
$tokenData['admin_id'] = $adminId;
$addedToAdmin++;
$token->setData($tokenData);
$token->save();
$_this->addModelToDelete($token);
$models['token'][] = $token;

//3) crate consumer-token
$consumerData = null;
$consumer = new Mage_OAuth_Model_Consumer();
$data = require 'consumerData.php';
$consumer->setData($data['create']);
$consumer->save();
$_this->addModelToDelete($consumer);
$models['consumer'][] = $consumer;

$tokenData = null;
$token = new Mage_OAuth_Model_Token();
$data = require 'tokenData.php';
$tokenData = $data['create'];
$tokenData['is_revoked']  = 0;
$tokenData['consumer_id'] = $consumer->getId();
$tokenData['admin_id']    = $adminId;
$addedToAdmin++;
$token->setData($tokenData);
$token->save();
$_this->addModelToDelete($token);
$models['token'][] = $token;


$models['added_to_admin'] = $addedToAdmin;

return $models;


