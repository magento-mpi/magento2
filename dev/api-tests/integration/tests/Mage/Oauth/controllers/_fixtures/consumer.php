<?php

$date = date('Ymd-His');
$keyCreate = substr(str_repeat(uniqid(), 6), 0, Mage_Oauth_Model_Consumer::KEY_LENGTH);
$secretCreate = substr(str_repeat(uniqid(), 6), 0, Mage_Oauth_Model_Consumer::SECRET_LENGTH);

/**
 * Model data
 *
 * @return array
 */
$consumerData = array(
    'name'                  => $date . ' Consumer Name Create Test',
    'key'                   => $keyCreate,
    'secret'                => $secretCreate,
    'callback_url'          => 'http://example.com/oauth_model/?oauthKey=key&oauthSecret=secret',
    'rejected_callback_url' => 'http://example.com/oauth_model/?rejected=1',
);

$consumer = new Mage_Oauth_Model_Consumer();
$consumer->setData($consumerData);
$consumer->save();

return $consumer;
